<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    pages: Object,
    auth: Object,
})

const pageToDelete = ref(null)

const confirmDelete = (page) => {
    pageToDelete.value = page
}

const deletePage = () => {
    router.delete(route('admin.pages.destroy', pageToDelete.value.id), {
        onSuccess: () => pageToDelete.value = null
    })
}

// Fonction pour obtenir l'URL de prévisualisation selon le statut de la page
const getPageUrl = (page) => {
    if (page.status === 'published') {
        return page.is_home ? '/' : `/${page.slug}`
    }
    return route('admin.pages.show', page.id)
}

</script>

<template>

    <Head title="Pages" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestion des pages
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- En-tête avec bouton de création -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Pages
                    </h1>
                    <Link v-if="$page.props.auth.user && pages.data.length > 0" :href="route('admin.pages.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Nouvelle page
                    </Link>
                </div>

                <!-- Message si aucune page -->
                <div v-if="!pages.data.length" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune page</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Commencez par créer votre première page.
                        </p>
                        <div class="mt-6">
                            <Link v-if="$page.props.auth.user" :href="route('admin.pages.create')"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Créer une page
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Liste des pages -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Titre
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Auteur
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dernière modification
                                    </th>
                                    <th class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="page in pages.data" :key="page.id"
                                    class="page-row transition-colors duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ page.title }}
                                                <span v-if="page.is_home"
                                                    class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                                    Accueil
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            page.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                        ]">
                                            {{ page.status === 'published' ? 'Publié' : 'Brouillon' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ page.author }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ page.updated_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <Link :href="getPageUrl(page)" class="text-gray-400 hover:text-gray-500">
                                            {{ page.status === 'published' ? 'Voir' : 'Prévisualiser' }}
                                            </Link>
                                            <Link v-if="$page.props.auth.user"
                                                :href="route('admin.pages.edit', page.id)"
                                                class="text-indigo-600 hover:text-indigo-900">
                                            Modifier
                                            </Link>
                                            <button v-if="$page.props.auth.user" @click="confirmDelete(page)"
                                                class="text-red-600 hover:text-red-900">
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmation de suppression -->
        <Modal :show="!!pageToDelete" @close="pageToDelete = null">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirmer la suppression
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Êtes-vous sûr de vouloir supprimer cette page ? Cette action est irréversible.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                        @click="pageToDelete = null">
                        Annuler
                    </button>
                    <button type="button"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                        @click="deletePage">
                        Supprimer
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped lang="postcss">
.page-row {
    @apply hover:bg-gray-50;
}

/* Effet de survol plus prononcé */
tr.page-row:hover {
    background-color: #f3f4f6;
    cursor: pointer;
}

/* Transition douce pour le survol */
tr.page-row {
    transition: background-color 0.2s ease-in-out;
}
</style>