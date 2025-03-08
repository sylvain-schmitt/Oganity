<script setup>
import InputError from '@/Components/InputError.vue'

defineProps({
    form: Object,
    parentPages: {
        type: Array,
        required: true
    },
    clearError: {
        type: Function,
        required: true
    },
    homePageExists: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <div class="space-y-6">
        <!-- Configuration de la page -->
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Configuration de la page
            </h3>

            <!-- Page d'accueil -->
            <div class="space-y-6">
                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="is_home" v-model="form.is_home" @change="clearError('is_home')" type="checkbox"
                            :disabled="homePageExists && !form.is_home"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 disabled:opacity-50" />
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_home" :class="{ 'text-gray-500': homePageExists && !form.is_home }"
                            class="font-medium text-gray-900">
                            Page d'accueil
                        </label>
                        <p v-if="!homePageExists || form.is_home" class="text-gray-500">
                            Définir cette page comme page d'accueil du site
                        </p>
                        <p v-else class="text-gray-500">
                            Une autre page est déjà définie comme page d'accueil
                        </p>
                    </div>
                </div>
                <InputError :message="form.errors.is_home" class="mt-2" />

                <!-- Ordre d'affichage -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">
                        Ordre d'affichage
                    </label>
                    <input type="number" id="sort_order" v-model="form.sort_order" @input="clearError('sort_order')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.sort_order" class="mt-2" />
                </div>

                <!-- Page parente -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">
                        Page parente
                    </label>
                    <select id="parent_id" v-model="form.parent_id" @change="clearError('parent_id')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option :value="null">Aucune page parente</option>
                        <option v-for="page in parentPages" :key="page.id" :value="page.id">
                            {{ page.title }}
                        </option>
                    </select>
                    <InputError :message="form.errors.parent_id" class="mt-2" />
                </div>
            </div>
        </div>
    </div>
</template>