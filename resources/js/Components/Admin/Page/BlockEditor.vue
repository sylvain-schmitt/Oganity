<script setup>
import { computed } from 'vue';
import TitleBlock from './Blocks/TitleBlock.vue';
import TextBlock from './Blocks/TextBlock.vue';
import TitleToolbar from './Toolbars/TitleToolbar.vue';
import TextToolbar from './Toolbars/TextToolbar.vue';

const props = defineProps({
    block: Object,
    styles: Object,
    isActive: Boolean,
    hasH1: Boolean
});

const emit = defineEmits(['update:content', 'update:styles', 'delete', 'activate']);

// Rendre le composant d'édition approprié selon le type de bloc
const renderBlockEditor = computed(() => {
    if (!props.block) return null;

    switch (props.block.type) {
        case 'title':
            return {
                component: TitleBlock,
                props: {
                    modelValue: props.block.content,
                    styles: props.styles || {},
                    blockId: props.block.id
                }
            };
        case 'text':
            return {
                component: TextBlock,
                props: {
                    modelValue: props.block.content,
                    styles: props.styles || {},
                    blockId: props.block.id
                }
            };
        default:
            return null;
    }
});

// Rendre la barre d'outils appropriée selon le type de bloc
const renderToolbar = computed(() => {
    if (!props.block || !props.isActive) return null;

    switch (props.block.type) {
        case 'title':
            return {
                component: TitleToolbar,
                props: {
                    blockId: props.block.id,
                    styles: props.styles || {},
                    hasH1: props.hasH1
                }
            };
        case 'text':
            return {
                component: TextToolbar,
                props: {
                    blockId: props.block.id,
                    styles: props.styles || {}
                }
            };
        default:
            return null;
    }
});

// Mettre à jour le contenu du bloc
const updateContent = (newContent) => {
    emit('update:content', props.block.id, newContent);
};

// Mettre à jour les styles du bloc
const updateStyles = (blockId, newStyles) => {
    emit('update:styles', blockId, newStyles);
};

// Supprimer le bloc
const deleteBlock = () => {
    emit('delete', props.block.id);
};

// Activer le bloc pour édition
const activateBlock = () => {
    emit('activate', props.block.id);
};
</script>

<template>
    <div class="editor-block relative bg-white shadow rounded-lg overflow-hidden mb-4"
        :class="{ 'ring-2 ring-blue-500': isActive }" @click="activateBlock">
        <!-- Barre d'outils de bloc -->
        <div class="flex items-center justify-between p-2 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center">
                <span class="material-icons text-gray-500 drag-handle mr-2">drag_indicator</span>
                <span class="text-xs font-medium text-gray-600">{{ block.type === 'title' ? 'Titre' : block.type === 'text' ? 'Texte' : block.type }}</span>
            </div>
            <div class="flex items-center">
                <button type="button" @click.stop="deleteBlock"
                    class="p-1 text-gray-500 hover:text-red-500 transition-colors">
                    <span class="material-icons text-sm">delete</span>
                </button>
            </div>
        </div>

        <!-- Contenu du bloc -->
        <div class="p-4">
            <component v-if="renderBlockEditor" :is="renderBlockEditor.component" v-bind="renderBlockEditor.props"
                @update:modelValue="updateContent" @update:styles="updateStyles" />
        </div>

        <!-- Barre d'outils d'édition (visible uniquement si le bloc est actif) -->
        <div v-if="isActive && renderToolbar" class="mt-2 p-3 bg-gray-50 rounded-md border border-gray-200">
            <component :is="renderToolbar.component" v-bind="renderToolbar.props" @update="updateStyles" />
        </div>
    </div>
</template> 