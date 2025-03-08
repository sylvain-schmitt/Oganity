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
const textStyles = computed(() => ({
    color: props.styles?.color || "#333333",
    textAlign: props.styles?.textAlign || "left",
    fontSize: `${props.styles?.fontSize || 16}px`,
    fontWeight: props.styles?.fontWeight || 'normal',
    lineHeight: props.styles?.lineHeight || '1.6',
    fontFamily: props.styles?.fontFamily || 'inherit'
}));

// Mettre à jour le contenu
const updateContent = (event) => {
    const newContent = { ...props.modelValue, content: event.target.innerHTML };
    emit("update:modelValue", newContent);
};

// Mettre à jour les styles
const updateStyles = () => {
    emit("update:styles", {
        color: textStyles.value.color,
        textAlign: textStyles.value.textAlign,
        fontSize: textStyles.value.fontSize,
        fontWeight: textStyles.value.fontWeight,
        lineHeight: textStyles.value.lineHeight,
        fontFamily: textStyles.value.fontFamily
    });
};
</script>

<template>
    <div :style="textStyles" class="prose max-w-none break-words" :contenteditable="!readonly" @blur="updateContent">
        <div v-if="!modelValue?.content" class="text-gray-400">Saisissez votre texte ici...</div>
        <div v-else v-html="modelValue.content"></div>
    </div>
</template>