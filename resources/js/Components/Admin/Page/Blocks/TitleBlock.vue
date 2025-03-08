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

// Contenu du titre
const content = computed(() => props.modelValue?.content || "");

// Tag HTML à utiliser
const tag = computed(() => props.styles?.tag || "h2");
</script>

<template>
    <component :is="tag" :style="titleStyles" class="break-words">
        {{ content || 'Titre' }}
    </component>
</template>
