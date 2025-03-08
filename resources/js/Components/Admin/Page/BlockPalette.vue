<script setup>
const props = defineProps({
    blockTypes: Array,
    disabled: Boolean
});

const emit = defineEmits(['add-block']);

const addBlock = (type, event) => {
    if (event) {
        event.preventDefault();
    }
    if (props.disabled) return;
    emit('add-block', type);
};
</script>

<template>
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
            <span class="material-icons text-blue-500 mr-1 text-sm">add_box</span>
            Ajouter un bloc
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <button v-for="blockType in blockTypes" :key="blockType.type"
                @click.prevent="addBlock(blockType.type, $event)"
                class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-all transform hover:-translate-y-1 hover:shadow-md"
                :class="{ 'opacity-50 cursor-not-allowed': blockType.disabled || disabled }"
                :disabled="blockType.disabled || disabled" :title="blockType.description">
                <div class="w-12 h-12 flex items-center justify-center bg-blue-100 rounded-full mb-3">
                    <span class="material-icons text-blue-600">{{ blockType.icon }}</span>
                </div>
                <span class="text-sm text-gray-700 font-medium">{{ blockType.label }}</span>
            </button>
        </div>
    </div>
</template>

<style scoped>
button {
    transition: all 0.2s ease;
}
</style>