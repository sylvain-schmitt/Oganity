<script setup>
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import DefaultLayout from '@/Layouts/Templates/DefaultLayout.vue'
import FullWidthLayout from '@/Layouts/Templates/FullWidthLayout.vue'
import SidebarLayout from '@/Layouts/Templates/SidebarLayout.vue'
import DefaultFooter from '@/Layouts/Templates/Footers/DefaultFooter.vue'
import FullWidthFooter from '@/Layouts/Templates/Footers/FullWidthFooter.vue'
import SidebarFooter from '@/Layouts/Templates/Footers/SidebarFooter.vue'
import TitleBlock from '@/Components/Admin/Page/Blocks/TitleBlock.vue'

const props = defineProps({
    page: {
        type: Object,
        required: true
    }
})

// Calcul des métadonnées pour le SEO
const metaTags = computed(() => {
    const meta = props.page.meta || {}
    return [
        { name: 'description', content: meta.description },
        { name: 'keywords', content: meta.keywords },
        { name: 'robots', content: meta.robots || 'index,follow' },
        { property: 'og:title', content: meta.og_title || props.page.title },
        { property: 'og:description', content: meta.og_description || meta.description },
        { property: 'og:image', content: meta.og_image },
        { property: 'og:type', content: meta.og_type || 'website' }
    ].filter(tag => tag.content)
})

// Fonction pour rendre un bloc selon son type
const renderBlock = (block) => {
    if (!block || !block.type) return null;

    switch (block.type) {
        case 'title':
            return {
                component: TitleBlock,
                props: {
                    modelValue: block.content,
                    styles: props.page.styles?.[block.id] || {},
                    readonly: true
                }
            };
        // Ajouter d'autres types de blocs ici
        default:
            return null;
    }
}

// Sélection du layout selon le template
const TemplateLayout = computed(() => {
    switch (props.page.template) {
        case 'full-width':
            return FullWidthLayout
        case 'sidebar':
            return SidebarLayout
        default:
            return DefaultLayout
    }
})

// Sélection du footer selon le template
const FooterComponent = computed(() => {
    switch (props.page.template) {
        case 'full-width':
            return FullWidthFooter
        case 'sidebar':
            return SidebarFooter
        default:
            return DefaultFooter
    }
})
</script>

<template>

    <Head>
        <title>{{ page.title }}</title>
        <meta v-for="tag in metaTags" :key="tag.name || tag.property" :name="tag.name" :property="tag.property"
            :content="tag.content" />
    </Head>

    <div class="min-h-screen bg-gray-100 flex flex-col">
        <main class="py-12 flex-grow">
            <div class="mx-auto sm:px-6 lg:px-8">
                <component :is="TemplateLayout" :title="page.title">
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
                </component>
            </div>
        </main>

        <component :is="FooterComponent" />
    </div>
</template>