<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TitleBlock from '@/Components/Admin/Page/Blocks/TitleBlock.vue'

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    displayMode: {
        type: String,
        default: 'preview'
    },
    can: {
        type: Object,
        default: () => ({
            edit: false,
            publish: false
        })
    }
})

// Vérifier si l'ID de la page est disponible
const pageId = computed(() => props.page.id)

// Calcul des métadonnées pour le SEO
const metaTags = computed(() => {
    const meta = props.page.meta || {}
    return [
        { name: 'description', content: meta.description },
        { name: 'keywords', content: meta.keywords },
        { name: 'robots', content: meta.robots || 'index,follow' },
        // Open Graph
        { property: 'og:title', content: meta.og_title || props.page.title },
        { property: 'og:description', content: meta.og_description || meta.description },
        { property: 'og:image', content: meta.og_image },
        { property: 'og:type', content: meta.og_type || 'website' }
    ].filter(tag => tag.content)
})

// Fonction pour rendre un bloc selon son type
const renderBlock = (block) => {
    if (!block || !block.type) return null;

    const blockStyles = props.page.styles?.[block.id] || {};
    
    // S'assurer que les styles ont une balise par défaut
    if (block.type === 'title' && !blockStyles.tag) {
        blockStyles.tag = 'h2';
    }

    switch (block.type) {
        case 'title':
            return {
                component: TitleBlock,
                props: {
                    modelValue: block.content,
                    styles: blockStyles,
                    blockId: block.id,
                    readonly: true
                }
            };
        // Ajouter d'autres types de blocs ici
        default:
            return null;
    }
}

// Classes CSS selon le template
const mainClasses = computed(() => {
    const template = props.page.template || 'default';

    switch (template) {
        case 'full-width':
            return 'bg-white shadow rounded-lg overflow-hidden w-full';
        case 'sidebar':
            return 'bg-white shadow rounded-lg overflow-hidden col-span-9 p-6';
        default: // template par défaut
            return 'bg-white shadow rounded-lg overflow-hidden w-full p-6';
    }
});

const containerClasses = computed(() => {
    const template = props.page.template || 'default';

    switch (template) {
        case 'full-width':
            return 'w-full';
        case 'sidebar':
            return 'grid grid-cols-12 gap-8';
        default: // template par défaut
            return 'max-w-7xl mx-auto';  // Vide car la largeur est gérée par le layout
    }
});
</script>

<template>

    <Head>
        <title>Prévisualisation - {{ page.title }}</title>
        <meta v-for="tag in metaTags" :key="tag.name || tag.property" :name="tag.name" :property="tag.property"
            :content="tag.content" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Prévisualisation: {{ page.title }}
                </h2>
                <div class="flex space-x-2">
                    <Link v-if="can.edit && pageId" :href="route('admin.pages.edit', pageId)"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Modifier
                    </Link>
                    <Link v-if="can.publish && page.status === 'draft' && pageId"
                        :href="route('admin.pages.publish', pageId)" method="patch"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Publier
                    </Link>
                </div>
            </div>
        </template>

        <!-- Alerte de prévisualisation -->
        <div v-if="page.status === 'draft'" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Mode prévisualisation - Cette page n'est pas encore publiée
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div :class="containerClasses">
                    <!-- Contenu principal -->
                    <main :class="mainClasses">
                        <!-- Pour le template full-width, le contenu est directement dans un conteneur sans padding -->
                        <div v-if="page.template === 'full-width'" class="w-full">
                            <div class="p-6">
                                <!-- Rendu des blocs de contenu -->
                                <div v-if="page.content && page.content.length > 0">
                                    <div v-for="block in page.content" :key="block.id" class="mb-6">
                                        <component v-if="renderBlock(block)" :is="renderBlock(block).component"
                                            v-bind="renderBlock(block).props" />
                                    </div>
                                </div>
                                <div v-else class="text-center py-12 text-gray-500">
                                    Aucun contenu à afficher
                                </div>
                            </div>
                        </div>

                        <!-- Pour les autres templates, le contenu est affiché normalement -->
                        <template v-else>
                            <!-- Rendu des blocs de contenu -->
                            <div v-if="page.content && page.content.length > 0">
                                <div v-for="block in page.content" :key="block.id" class="mb-6">
                                    <component v-if="renderBlock(block)" :is="renderBlock(block).component"
                                        v-bind="renderBlock(block).props" />
                                </div>
                            </div>
                            <div v-else class="text-center py-12 text-gray-500">
                                Aucun contenu à afficher
                            </div>
                        </template>
                    </main>

                    <!-- Barre latérale (si template avec sidebar) -->
                    <aside v-if="page.template === 'sidebar'" class="col-span-3">
                        <div class="bg-white shadow rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                                Navigation
                            </h2>
                            <!-- Contenu de la sidebar -->
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">
                                    Cette barre latérale sera personnalisable dans une future mise à jour.
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>