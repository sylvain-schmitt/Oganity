<script setup>
const props = defineProps({
    blockTypes: Array,
    disabled: Boolean
});

const emit = defineEmits(['add-block']);

const addBlock = (type) => {
    if (props.disabled) return;
    emit('add-block', type);
};
</script>

<template>
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Ajouter un bloc</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
            <button v-for="blockType in blockTypes" :key="blockType.type" @click="addBlock(blockType.type)"
                class="flex flex-col items-center justify-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                :class="{ 'opacity-50 cursor-not-allowed': blockType.disabled || disabled }"
                :disabled="blockType.disabled || disabled" :title="blockType.description">
                <span class="material-icons text-gray-600 mb-1">{{ blockType.icon }}</span>
                <span class="text-xs text-gray-700">{{ blockType.label }}</span>
            </button>
        </div>
    </div>
</template>