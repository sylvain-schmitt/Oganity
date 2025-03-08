<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ContentTab from '@/Components/Admin/Page/Tabs/ContentTab.vue'
import SeoTab from '@/Components/Admin/Page/Tabs/SeoTab.vue'
import OpenGraphTab from '@/Components/Admin/Page/Tabs/OpenGraphTab.vue'
import SettingsTab from '@/Components/Admin/Page/Tabs/SettingsTab.vue'
import TabNavigation from '@/Components/Admin/Page/TabNavigation.vue'
import ConfirmLeaveModal from '@/Components/Admin/Page/ConfirmLeaveModal.vue'

const props = defineProps({
    page: Object,
    templates: Object,
    statuses: Object,
    availableParents: {
        type: Array,
        default: () => []
    },
    parentPages: {
        type: Array,
        default: () => []
    },
    homePageExists: {
        type: Boolean,
        default: false
    }
})

// État pour suivre si des modifications ont été apportées
const hasContentChanged = ref(false);

// Initialiser le formulaire avec les données de la page
const form = useForm({
    title: props.page.title || '',
    slug: props.page.slug || '',
    content: props.page.content || [],
    styles: props.page.styles || {},
    template: props.page.template || '',
    status: props.page.status || 'draft',
    meta_title: props.page.meta_title || '',
    meta_description: props.page.meta_description || '',
    meta_keywords: props.page.meta_keywords || '',
    meta_robots: props.page.meta_robots || 'index follow',
    meta_google_verification: props.page.meta_google_verification || '',
    meta_bing_verification: props.page.meta_bing_verification || '',
    meta_yandex_verification: props.page.meta_yandex_verification || '',
    og_title: props.page.og_title || '',
    og_description: props.page.og_description || '',
    og_image: props.page.og_image || '',
    og_type: props.page.og_type || 'website',
    is_home: props.page.is_home || false,
    sort_order: props.page.sort_order || 0,
    parent_id: props.page.parent_id || null
})

// État pour le contenu de l'éditeur
const pageContent = ref({
    content: props.page.content || [],
    styles: props.page.styles || {}
});

// Surveiller les changements du contenu de l'éditeur
watch(pageContent, (newValue) => {
    form.content = newValue.content;
    form.styles = newValue.styles;
}, { deep: true });

const currentTab = ref('main')

const submit = () => {
    form.put(route('admin.pages.update', props.page.id), {
        onSuccess: () => {
            hasContentChanged.value = false;
            router.visit(route('admin.pages.index'))
        }
    })
}

const tabs = {
    main: 'Contenu',
    seo: 'SEO',
    social: 'Réseaux sociaux',
    config: 'Configuration'
}

// État pour le modal de confirmation
const showConfirmModal = ref(false)

// Vérifie si le formulaire a été modifié
const hasChanges = computed(() => {
    // Utilisons une approche plus directe pour comparer les valeurs importantes
    if (form.title !== props.page.title) return true;
    if (form.slug !== props.page.slug) return true;
    if (form.template !== props.page.template) return true;
    if (form.status !== props.page.status) return true;
    if (form.meta_title !== (props.page.meta_title || '')) return true;
    if (form.meta_description !== (props.page.meta_description || '')) return true;
    if (form.meta_keywords !== (props.page.meta_keywords || '')) return true;
    if (form.meta_robots !== (props.page.meta_robots || 'index follow')) return true;
    if (form.og_title !== (props.page.og_title || '')) return true;
    if (form.og_description !== (props.page.og_description || '')) return true;
    if (form.og_image !== (props.page.og_image || '')) return true;
    if (form.og_type !== (props.page.og_type || 'website')) return true;
    if (form.is_home !== (props.page.is_home || false)) return true;
    if (form.sort_order !== (props.page.sort_order || 0)) return true;
    if (form.parent_id !== (props.page.parent_id || null)) return true;

    // Comparaison spéciale pour le contenu (tableau d'objets)
    if (JSON.stringify(form.content) !== JSON.stringify(props.page.content)) return true;
    if (JSON.stringify(form.styles) !== JSON.stringify(props.page.styles)) return true;

    // Prendre en compte hasContentChanged
    if (hasContentChanged.value) return true;

    return false;
})

// Gestion de la navigation
const handleCancel = () => {
    if (hasChanges.value) {
        showConfirmModal.value = true
    } else {
        router.visit(route('admin.pages.index'))
    }
}

// Fonction pour confirmer l'annulation
const confirmCancel = () => {
    router.visit(route('admin.pages.index'))
}

// Fonction pour suivre les modifications du contenu
const onContentChanged = (changed) => {
    hasContentChanged.value = changed;
}

// Réinitialise l'erreur quand un champ est modifié
const clearError = (fieldName) => {
    form.clearErrors(fieldName);
}

// Surveille les modifications des champs pour effacer les erreurs
watch(() => form.title, () => clearError('title'));
watch(() => form.content, () => clearError('content'));
watch(() => form.template, () => clearError('template'));
watch(() => form.status, () => clearError('status'));
</script>

<template>

    <Head title="Modifier une page" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Modifier la page {{ props.page.title }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                            <!-- Navigation des onglets -->
                            <TabNavigation v-model:currentTab="currentTab" :tabs="tabs" :errors="form.errors" />

                            <!-- Contenu des onglets avec transition -->
                            <div class="relative bg-white shadow rounded-lg p-6">
                                <transition name="fade" mode="out-in">
                                    <div :key="currentTab">
                                        <ContentTab v-if="currentTab === 'main'" :form="form" :templates="templates"
                                            :statuses="statuses" :clear-error="clearError" v-model="pageContent"
                                            :initial-data="props.page" @content-changed="onContentChanged" />
                                        <SeoTab v-if="currentTab === 'seo'" :form="form" :clear-error="clearError" />
                                        <OpenGraphTab v-if="currentTab === 'social'" :form="form"
                                            :clear-error="clearError" />
                                        <SettingsTab v-if="currentTab === 'config'" :form="form"
                                            :available-parents="availableParents" :parent-pages="parentPages"
                                            :home-page-exists="homePageExists" :clear-error="clearError" />
                                    </div>
                                </transition>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex items-center justify-end px-6 py-4 bg-gray-50">
                                <button type="button" @click="handleCancel"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    Annuler
                                </button>

                                <button type="submit" :disabled="form.processing"
                                    class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation -->
        <ConfirmLeaveModal :show="showConfirmModal" @close="showConfirmModal = false" @confirm="confirmCancel" />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>