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

const emit = defineEmits(["update:modelValue", "update:styles"]);

// Calculer les styles CSS
const titleStyles = computed(() => ({
    color: props.styles?.color || "#000000",
    textAlign: props.styles?.textAlign || "left",
    fontSize: `${props.styles?.fontSize || 24}px`,
    fontWeight: props.styles?.fontWeight || 'bold',
    lineHeight: props.styles?.lineHeight || '1.2',
    fontFamily: props.styles?.fontFamily || 'inherit'
}));

// Mettre à jour le contenu
const updateContent = (event) => {
    const newContent = { ...props.modelValue, content: event.target.innerText };
    emit("update:modelValue", newContent);
};
</script>

<template>
    <component :is="styles?.tag || 'h2'" :style="titleStyles" class="break-words" :contenteditable="!readonly"
        @blur="updateContent" @keydown.enter.prevent>
        {{ modelValue?.content || 'Titre' }}
    </component>
</template>