<script setup>
import { ref, computed, watch, provide, nextTick } from 'vue';
import draggable from 'vuedraggable';
import BlockPalette from './BlockPalette.vue';
import BlockEditor from './BlockEditor.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update:modelValue', 'content-change']);

// Référence locale au contenu et aux styles
const content = ref(props.modelValue.content || []);
const styles = ref(props.modelValue.styles || {});

// Bloc actuellement sélectionné pour édition
const activeBlockId = ref(null);

// Vérifier si un H1 existe déjà dans le contenu
const hasH1 = computed(() => {
    return content.value.some(block => {
        const blockId = block.id;
        return block.type === 'title' && styles.value[blockId]?.tag === 'h1';
    });
});

// Fournir la fonction pour vérifier si un H1 existe
provide('checkH1Exists', () => hasH1.value);

// Fournir une fonction pour vérifier si le bloc actuel est un H1
provide('currentBlockIsH1', (blockId) => {
    return styles.value[blockId]?.tag === 'h1';
});

// Surveiller les changements du modelValue
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        content.value = newValue.content || [];
        styles.value = newValue.styles || {};
    }
}, { deep: true });

// Types de blocs disponibles
const blockTypes = [
    {
        type: 'title',
        label: 'Titre',
        icon: 'title',
        description: 'Ajouter un titre ou un sous-titre'
    },
    {
        type: 'text',
        label: 'Texte',
        icon: 'text_fields',
        description: 'Ajouter un paragraphe de texte'
    },
    {
        type: 'image',
        label: 'Image',
        icon: 'image',
        description: 'Ajouter une image',
        disabled: true
    },
    {
        type: 'button',
        label: 'Bouton',
        icon: 'smart_button',
        description: 'Ajouter un bouton cliquable',
        disabled: true
    }
];

// Ajouter un nouveau bloc
const addBlock = (type) => {
    // Si le type est désactivé, ne rien faire
    if (blockTypes.find(bt => bt.type === type)?.disabled) {
        return;
    }

    // Créer un nouvel ID unique pour le bloc
    const blockId = Date.now().toString();

    // Créer un nouveau bloc selon le type
    let newBlock;

    if (type === 'title') {
        newBlock = {
            id: blockId,
            type: 'title',
            content: { content: '' } // Contenu vide pour utiliser le placeholder
        };

        // Initialiser les styles pour ce bloc
        // Si un H1 existe déjà, on utilise H2 par défaut
        styles.value[blockId] = {
            color: "#000000",
            textAlign: "left",
            fontSize: 24,
            tag: hasH1.value ? 'h2' : 'h1' // Utiliser H1 si aucun H1 n'existe, sinon H2
        };
    } else if (type === 'text') {
        newBlock = {
            id: blockId,
            type: 'text',
            content: { content: '' } // Contenu vide pour utiliser le placeholder
        };

        // Initialiser les styles pour ce bloc
        styles.value[blockId] = {
            color: "#333333",
            textAlign: "left",
            fontSize: 16,
            fontWeight: 'normal',
            lineHeight: '1.6'
        };
    }
    // Ajouter d'autres types de blocs ici

    // Ajouter le bloc au contenu
    if (newBlock) {
        content.value.push(newBlock);
        emitUpdate();

        // Activer le nouveau bloc pour édition
        nextTick(() => {
            activeBlockId.value = blockId;
        });
    }
};

// Mettre à jour un bloc existant
const updateBlockContent = (blockId, newContent) => {
    const blockIndex = content.value.findIndex(block => block.id === blockId);
    if (blockIndex !== -1) {
        content.value[blockIndex].content = newContent;
        emitUpdate();
    }
};

// Mettre à jour les styles d'un bloc
const updateBlockStyles = (blockId, newStyles) => {
    // Vérifier si les styles existent déjà pour ce bloc
    if (!styles.value[blockId]) {
        styles.value[blockId] = {};
    }

    // Mettre à jour les styles avec les nouvelles valeurs
    styles.value[blockId] = {
        ...styles.value[blockId],
        ...newStyles
    };

    emitUpdate();
};

// Supprimer un bloc
const deleteBlock = (blockId) => {
    const blockIndex = content.value.findIndex(block => block.id === blockId);
    if (blockIndex !== -1) {
        content.value.splice(blockIndex, 1);

        // Si le bloc supprimé était actif, désactiver la sélection
        if (activeBlockId.value === blockId) {
            activeBlockId.value = null;
        }

        emitUpdate();
    }
};

// Activer un bloc pour édition
const activateBlock = (blockId) => {
    activeBlockId.value = blockId;
};

// Émettre les mises à jour
const emitUpdate = () => {
    emit('update:modelValue', {
        content: content.value,
        styles: styles.value
    });
    emit('content-change');
};
</script>

<template>
    <div class="page-editor">
        <!-- Palette de blocs -->
        <BlockPalette :block-types="blockTypes" :disabled="false" @add-block="addBlock" />

        <!-- Éditeur de blocs -->
        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Contenu de la page</h3>
            <div class="space-y-4">
                <draggable v-model="content" group="blocks" item-key="id" handle=".drag-handle"
                    ghost-class="ghost-block" @change="emitUpdate">
                    <template #item="{ element: block }">
                        <BlockEditor :block="block" :styles="styles[block.id] || {}"
                            :is-active="activeBlockId === block.id" :has-h1="hasH1" @update:content="updateBlockContent"
                            @update:styles="updateBlockStyles" @delete="deleteBlock" @activate="activateBlock" />
                    </template>
                </draggable>

                <!-- Message si aucun contenu -->
                <div v-if="content.length === 0" class="text-center py-8 text-gray-500">
                    <p class="mb-2">Aucun contenu. Ajoutez des blocs pour créer votre page.</p>
                    <div class="flex justify-center space-x-2">
                        <button v-for="blockType in blockTypes.filter(bt => !bt.disabled)" :key="blockType.type"
                            @click="addBlock(blockType.type)"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                            <span class="material-icons text-xs mr-1 align-text-bottom">{{ blockType.icon }}</span>
                            {{ blockType.label }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.page-editor {
    width: 100%;
}

.editor-block {
    transition: all 0.2s ease;
}

.ghost-block {
    opacity: 0.5;
    background: #c8ebfb;
}

.drag-handle {
    cursor: move;
}

.toolbar-group {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.toolbar-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
}

/* Supprimer la flèche native du select dans différents navigateurs */
select {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
}

select::-ms-expand {
    display: none !important;
}

/* Ajout d'icônes Material Design */
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
</style>
