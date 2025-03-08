<script setup>
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    form: Object,
    clearError: {
        type: Function,
        required: true
    }
})
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Paramètres Open Graph
            </h3>
            <div class="grid grid-cols-1 gap-6">
                <!-- Open Graph titre -->
                <div>
                    <label for="og_title" class="block text-sm font-medium text-gray-700">
                        Titre Open Graph
                    </label>
                    <input type="text" id="og_title" v-model="form.og_title" @input="clearError('og_title')"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.og_title }" />
                    <InputError :message="form.errors.og_title" class="mt-2" />
                </div>

                <!-- Open Graph description -->
                <div>
                    <label for="og_description" class="block text-sm font-medium text-gray-700">
                        Description Open Graph
                    </label>
                    <textarea id="og_description" v-model="form.og_description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.og_description }"></textarea>
                    <InputError :message="form.errors.og_description" class="mt-2" />
                </div>

                <!-- Open Graph image -->
                <div>
                    <label for="og_image" class="block text-sm font-medium text-gray-700">
                        Image Open Graph
                    </label>
                    <input type="text" id="og_image" v-model="form.og_image"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.og_image }" />
                    <InputError :message="form.errors.og_image" class="mt-2" />
                </div>

                <!-- Open Graph type -->
                <div>
                    <label for="og_type" class="block text-sm font-medium text-gray-700">
                        Type Open Graph
                    </label>
                    <select id="og_type" v-model="form.og_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.og_type }">
                        <option value="website">Site web</option>
                        <option value="article">Article</option>
                    </select>
                    <InputError :message="form.errors.og_type" class="mt-2" />
                </div>
            </div>
        </div>
    </div>
</template>