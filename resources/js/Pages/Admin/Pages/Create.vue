<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ContentTab from '@/Components/Admin/Page/Tabs/ContentTab.vue'
import SeoTab from '@/Components/Admin/Page/Tabs/SeoTab.vue'
import OpenGraphTab from '@/Components/Admin/Page/Tabs/OpenGraphTab.vue'
import SettingsTab from '@/Components/Admin/Page/Tabs/SettingsTab.vue'
import TabNavigation from '@/Components/Admin/Page/TabNavigation.vue'
import ConfirmLeaveModal from '@/Components/Admin/Page/ConfirmLeaveModal.vue'

const { templates, statuses, availableParents, parentPages, homePageExists = false } = defineProps({
    templates: Object,
    statuses: Object,
    availableParents: {
        type: Array,
        default: () => []
    },
    parentPages: Array,
    homePageExists: Boolean,
})

// État des onglets
const currentTab = ref('main')
const tabs = {
    main: 'Contenu',
    seo: 'SEO',
    social: 'Réseaux sociaux',
    config: 'Configuration'
}

// Formulaire
const form = useForm({
    title: '',
    slug: '',
    content: [],
    styles: {},
    template: 'default',
    status: 'draft',
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    meta_robots: 'index follow',
    meta_google_verification: '',
    meta_bing_verification: '',
    meta_yandex_verification: '',
    og_title: '',
    og_description: '',
    og_image: '',
    og_type: 'website',
    is_home: false,
    sort_order: 0,
    parent_id: null
})

// État pour le contenu de l'éditeur
const pageContent = ref({
    content: [],
    styles: {}
});

// Surveiller les changements du contenu de l'éditeur
watch(pageContent, (newValue) => {
    form.content = newValue.content;
    form.styles = newValue.styles;
}, { deep: true });

// Réinitialise l'erreur quand un champ est modifié
const clearError = (fieldName) => {
    form.clearErrors(fieldName)
}

// Surveille les modifications des champs pour effacer les erreurs
watch(() => form.title, () => clearError('title'))
watch(() => form.content, () => clearError('content'))
watch(() => form.template, () => clearError('template'))
watch(() => form.status, () => clearError('status'))
watch(() => form.meta_title, () => clearError('meta_title'))
watch(() => form.meta_description, () => clearError('meta_description'))
watch(() => form.meta_keywords, () => clearError('meta_keywords'))
watch(() => form.meta_robots, () => clearError('meta_robots'))
watch(() => form.meta_google_verification, () => clearError('meta_google_verification'))
watch(() => form.meta_bing_verification, () => clearError('meta_bing_verification'))
watch(() => form.meta_yandex_verification, () => clearError('meta_yandex_verification'))
watch(() => form.og_title, () => clearError('og_title'))
watch(() => form.og_description, () => clearError('og_description'))
watch(() => form.og_image, () => clearError('og_image'))
watch(() => form.og_type, () => clearError('og_type'))
watch(() => form.is_home, () => clearError('is_home'))
watch(() => form.sort_order, () => clearError('sort_order'))
watch(() => form.parent_id, () => clearError('parent_id'))

// Ajout d'une computed property pour vérifier si le formulaire a été modifié
const isFormDirty = computed(() => {
    const formData = form.data()
    return (
        formData.title !== '' ||
        formData.content.length > 0 ||
        formData.template !== 'default' ||
        formData.status !== 'draft' ||
        formData.meta_title !== '' ||
        formData.meta_description !== '' ||
        formData.meta_keywords !== '' ||
        formData.meta_robots !== 'index follow' ||
        formData.meta_google_verification !== '' ||
        formData.meta_bing_verification !== '' ||
        formData.meta_yandex_verification !== '' ||
        formData.og_title !== '' ||
        formData.og_description !== '' ||
        formData.og_image !== '' ||
        formData.og_type !== 'website' ||
        formData.is_home !== false ||
        formData.sort_order !== 0 ||
        formData.parent_id !== null
    )
})

const showConfirmModal = ref(false)

const submit = () => {
    form.post(route('admin.pages.store'))
}

// Fonction pour gérer le clic sur le bouton Annuler
const handleCancel = () => {
    if (isFormDirty.value) {
        showConfirmModal.value = true
    } else {
        window.location = route('admin.pages.index')
    }
}

// Fonction pour confirmer l'annulation
const confirmCancel = () => {
    router.visit(route('admin.pages.index'))
}
</script>

<template>

    <Head title="Créer une page" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Créer une page
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
                                            :statuses="statuses" :clear-error="clearError" v-model="pageContent" />
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
                                    {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
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