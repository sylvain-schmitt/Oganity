<script setup>
import { computed, ref, onMounted, watch } from "vue";

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

// Référence à l'élément éditable
const editableDiv = ref(null);

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

// Gérer spécifiquement l'événement de collage
const handlePaste = (event) => {
    // Empêcher le comportement par défaut pour gérer nous-mêmes le collage
    event.preventDefault();

    // Récupérer le texte brut du presse-papiers
    const text = event.clipboardData.getData('text/plain');

    // Insérer le texte à la position du curseur
    document.execCommand('insertText', false, text);

    // Mettre à jour le contenu après le collage
    const newContent = { ...props.modelValue, content: editableDiv.value.innerHTML };
    emit("update:modelValue", newContent);
};

// Gérer le focus pour supprimer le placeholder
const handleFocus = () => {
    if (!props.modelValue?.content && editableDiv.value) {
        // Si le contenu est vide et que l'utilisateur clique dans la zone éditable,
        // on efface le placeholder en vidant le contenu de l'élément
        editableDiv.value.innerHTML = '';
    }
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

// Initialiser le contenu au montage du composant
onMounted(() => {
    if (editableDiv.value && props.modelValue?.content) {
        editableDiv.value.innerHTML = props.modelValue.content;
    }
});

// Observer les changements de modelValue pour mettre à jour le contenu
watch(() => props.modelValue?.content, (newContent) => {
    if (editableDiv.value && newContent) {
        editableDiv.value.innerHTML = newContent;
    }
});
</script>

<template>
    <div ref="editableDiv" :style="textStyles" class="prose max-w-none break-words" :contenteditable="!readonly"
        @blur="updateContent" @paste="handlePaste" @focus="handleFocus"
        :data-placeholder="!modelValue?.content ? 'Saisissez votre texte ici...' : ''"></div>
</template>

<style scoped>
[contenteditable]:empty:before {
    content: attr(data-placeholder);
    color: #9ca3af;
    /* Tailwind gray-400 */
    font-style: italic;
    pointer-events: none;
}
</style>