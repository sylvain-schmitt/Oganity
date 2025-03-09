<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: Object,
    styles: Object,
    readonly: {
        type: Boolean,
        default: false
    },
    blockId: String
});

const emit = defineEmits(['update:modelValue']);

// Calculer les styles CSS pour le titre
const titleStyles = computed(() => {
    return {
        color: props.styles?.color || '#000000',
        fontSize: `${props.styles?.fontSize || 24}px`,
        textAlign: props.styles?.textAlign || 'left',
        fontWeight: props.styles?.fontWeight || 'bold'
    };
});

// Mettre à jour le contenu
const updateContent = (event) => {
    const newContent = { ...props.modelValue, content: event.target.innerText };
    emit("update:modelValue", newContent);
};

// Vérifier si c'est un H1
const isH1 = computed(() => {
    return props.styles?.tag === 'h1';
});
</script>

<template>
    <div class="relative">
        <!-- Badge H1 -->
        <div v-if="isH1" class="absolute -left-8 top-1/2 transform -translate-y-1/2 bg-blue-500 text-white text-xs font-bold px-1.5 py-0.5 rounded">
            H1
        </div>
        
        <component 
            :is="styles?.tag || 'h2'" 
            :style="titleStyles" 
            class="break-words" 
            :class="{ 'h1-title': isH1 }"
            :contenteditable="!readonly"
            @blur="updateContent" 
            @keydown.enter.prevent
        >
            {{ modelValue?.content || 'Titre' }}
        </component>
    </div>
</template>

<style scoped>
.h1-title {
    position: relative;
    padding-left: 0.25rem;
    border-left: 3px solid #3b82f6; /* blue-500 */
}
</style>