<script setup>
import { computed, ref, watch } from "vue";

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

// Contenu local pour l'édition
const localContent = ref(props.modelValue?.content || "");

// Styles locaux pour l'édition
const localStyles = ref({
    color: props.styles?.color || "#333333",
    textAlign: props.styles?.textAlign || "left",
    fontSize: props.styles?.fontSize || 16,
    fontWeight: props.styles?.fontWeight || 'normal',
    lineHeight: props.styles?.lineHeight || '1.6',
    fontFamily: props.styles?.fontFamily || 'inherit'
});

// Surveiller les changements des props
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        localContent.value = newValue.content || "";
    }
}, { deep: true });

watch(() => props.styles, (newValue) => {
    if (newValue) {
        localStyles.value = {
            color: newValue.color || "#333333",
            textAlign: newValue.textAlign || "left",
            fontSize: newValue.fontSize || 16,
            fontWeight: newValue.fontWeight || 'normal',
            lineHeight: newValue.lineHeight || '1.6',
            fontFamily: newValue.fontFamily || 'inherit'
        };
    }
}, { deep: true });

// Calculer les styles CSS
const textStyles = computed(() => ({
    color: localStyles.value.color,
    textAlign: localStyles.value.textAlign,
    fontSize: `${localStyles.value.fontSize}px`,
    fontWeight: localStyles.value.fontWeight,
    lineHeight: localStyles.value.lineHeight,
    fontFamily: localStyles.value.fontFamily
}));

// Mettre à jour le contenu
const updateContent = (event) => {
    localContent.value = event.target.value;
    emit("update:modelValue", { content: localContent.value });
};

// Mettre à jour les styles
const updateStyles = () => {
    emit("update:styles", localStyles.value);
};
</script>

<template>
    <div>
        <!-- Mode édition -->
        <template v-if="!readonly">
            <div class="mb-2">
                <textarea v-model="localContent" @input="updateContent" rows="4"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :style="textStyles" placeholder="Saisissez votre texte ici..."></textarea>
            </div>
        </template>

        <!-- Mode lecture seule (affichage) -->
        <template v-else>
            <p :style="textStyles" class="whitespace-pre-wrap">
                {{ localContent }}
            </p>
        </template>
    </div>
</template>