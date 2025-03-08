<script setup>
import ErrorBubble from '@/Components/UI/ErrorBubble.vue'

const props = defineProps({
    currentTab: {
        type: String,
        required: true
    },
    tabs: {
        type: Object,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:currentTab'])

// Compte les erreurs par onglet
const getTabErrorCount = (tabKey) => {

    const errorMapping = {
        main: ['title', 'content', 'template', 'status'],
        seo: ['meta_title', 'meta_description', 'meta_keywords', 'meta_robots', 'meta_google_verification', 'meta_bing_verification', 'meta_yandex_verification'],
        social: ['og_title', 'og_description', 'og_image', 'og_type'],
        config: ['sort_order', 'parent_id', 'is_home']
    }

    const count = errorMapping[tabKey]?.filter(field => props.errors[field]).length || 0

    return count
}
</script>

<template>
    <div class="border-b border-gray-200 bg-white rounded-t-lg shadow">
        <nav class="-mb-px flex space-x-8 px-6">
            <button v-for="(label, key) in tabs" :key="key" type="button"
                class="relative group whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                :class="[
                    currentTab === key
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]" @click="emit('update:currentTab', key)">
                {{ label }}
                <ErrorBubble :count="getTabErrorCount(key)" />
                <span
                    class="absolute inset-x-0 bottom-0 h-0.5 bg-indigo-200 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-200"
                    :class="{ 'scale-x-100': currentTab === key }">
                </span>
            </button>
        </nav>
    </div>
</template>

<style scoped>
/* Animation de l'indicateur d'erreur */
.error-indicator {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}
</style>