<script setup>
import InputError from '@/Components/InputError.vue'

defineProps({
    form: Object,
    clearError: {
        type: Function,
        required: true
    }
})
</script>

<template>
    <div class="space-y-6">
        <!-- SEO de base -->
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                SEO de base
            </h3>
            <div class="grid grid-cols-1 gap-6">
                <!-- Meta Title -->
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">
                        Titre SEO
                    </label>
                    <input type="text" id="meta_title" v-model="form.meta_title" @input="clearError('meta_title')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_title" class="mt-2" />
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">
                        Meta Description
                    </label>
                    <textarea id="meta_description" v-model="form.meta_description"
                        @input="clearError('meta_description')" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <InputError :message="form.errors.meta_description" class="mt-2" />
                </div>

                <!-- Meta Keywords -->
                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700">
                        Meta Keywords
                    </label>
                    <input type="text" id="meta_keywords" v-model="form.meta_keywords"
                        @input="clearError('meta_keywords')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_keywords" class="mt-2" />
                </div>

                <!-- Meta Robots -->
                <div>
                    <label for="meta_robots" class="block text-sm font-medium text-gray-700">
                        Meta Robots
                    </label>
                    <input type="text" id="meta_robots" v-model="form.meta_robots" @input="clearError('meta_robots')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_robots" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Balises de vérification -->
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Balises de vérification
            </h3>
            <div class="grid grid-cols-1 gap-6">
                <!-- Google -->
                <div>
                    <label for="meta_google_verification" class="block text-sm font-medium text-gray-700">
                        Google Search Console
                    </label>
                    <input type="text" id="meta_google_verification" v-model="form.meta_google_verification"
                        @input="clearError('meta_google_verification')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_google_verification" class="mt-2" />
                </div>

                <!-- Bing -->
                <div>
                    <label for="meta_bing_verification" class="block text-sm font-medium text-gray-700">
                        Bing Webmaster
                    </label>
                    <input type="text" id="meta_bing_verification" v-model="form.meta_bing_verification"
                        @input="clearError('meta_bing_verification')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_bing_verification" class="mt-2" />
                </div>

                <!-- Yandex -->
                <div>
                    <label for="meta_yandex_verification" class="block text-sm font-medium text-gray-700">
                        Yandex Webmaster
                    </label>
                    <input type="text" id="meta_yandex_verification" v-model="form.meta_yandex_verification"
                        @input="clearError('meta_yandex_verification')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    <InputError :message="form.errors.meta_yandex_verification" class="mt-2" />
                </div>
            </div>
        </div>
    </div>
</template>