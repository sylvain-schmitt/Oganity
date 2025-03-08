<script setup>
import { ref, computed, watch, provide, nextTick, onMounted, onBeforeUnmount } from 'vue';
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
    }
    // Autres types de blocs à ajouter ici
];

// Générer un ID unique pour un nouveau bloc
const generateUniqueId = () => {
    return 'block_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
};

// État pour contrôler l'affichage du menu déroulant
const showAddBlockMenu = ref(false);

// Fonction pour basculer l'affichage du menu
const toggleAddBlockMenu = () => {
    showAddBlockMenu.value = !showAddBlockMenu.value;
};

// Fonction pour fermer le menu lorsque l'utilisateur clique en dehors
const closeMenuOnClickOutside = (event) => {
    if (showAddBlockMenu.value) {
        showAddBlockMenu.value = false;
    }
};

// Ajouter et supprimer l'écouteur d'événements pour les clics en dehors
onMounted(() => {
    document.addEventListener('click', closeMenuOnClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeMenuOnClickOutside);
});

// Ajouter un nouveau bloc
const addBlock = (type) => {
    const newBlock = {
        id: generateUniqueId(),
        type: type,
        content: {}
    };

    // Initialiser les styles par défaut selon le type de bloc
    switch (type) {
        case 'title':
            styles.value[newBlock.id] = {
                tag: hasH1.value ? 'h2' : 'h1',
                textAlign: 'left',
                color: '#000000',
                fontSize: 24
            };
            newBlock.content = { content: '' };
            break;
        case 'text':
            styles.value[newBlock.id] = {
                textAlign: 'left',
                color: '#333333',
                fontSize: 16,
                fontWeight: 'normal',
                lineHeight: '1.6'
            };
            newBlock.content = { content: '' };
            break;
        // Ajouter d'autres types de blocs ici
    }

    // Ajouter le bloc au contenu
    content.value.push(newBlock);

    // Activer le nouveau bloc pour édition
    nextTick(() => {
        activeBlockId.value = newBlock.id;
    });

    // Émettre les changements
    emitChanges();
};

// Mettre à jour le contenu d'un bloc
const updateBlockContent = (blockId, newContent) => {
    const blockIndex = content.value.findIndex(block => block.id === blockId);
    if (blockIndex !== -1) {
        content.value[blockIndex].content = newContent;
        emitChanges();
    }
};

// Mettre à jour les styles d'un bloc
const updateBlockStyles = (blockId, newStyles) => {
    styles.value[blockId] = { ...styles.value[blockId], ...newStyles };
    emitChanges();
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

        emitChanges();
    }
};

// Activer un bloc pour édition
const activateBlock = (blockId) => {
    activeBlockId.value = blockId;
};

// Émettre les changements
const emitChanges = () => {
    const updatedValue = {
        content: content.value,
        styles: styles.value
    };

    emit('update:modelValue', updatedValue);
    emit('content-change', updatedValue);
};

// Gérer le réordonnancement des blocs
const handleReorder = () => {
    emitChanges();
};
</script>

<template>
    <div class="page-editor">
        <BlockPalette :block-types="blockTypes" @add-block="addBlock" />

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-4">
                <div v-if="content.length === 0" class="text-center py-8">
                    <p class="text-gray-500 mb-4">Aucun bloc n'a été ajouté à cette page.</p>
                    <button type="button" @click="addBlock('title')"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                        Ajouter un titre
                    </button>
                </div>

                <div v-else>
                    <draggable v-model="content" group="blocks" item-key="id" handle=".drag-handle"
                        ghost-class="ghost-block" @end="handleReorder">
                        <template #item="{ element: block }">
                            <BlockEditor :block="block" :styles="styles[block.id] || {}"
                                :is-active="activeBlockId === block.id" :has-h1="hasH1"
                                @update:content="updateBlockContent" @update:styles="updateBlockStyles"
                                @delete="deleteBlock" @activate="activateBlock" />
                        </template>
                    </draggable>

                    <!-- Boutons simples pour ajouter un bloc -->
                    <div class="flex justify-center mt-4 space-x-2">
                        <button v-for="blockType in blockTypes" :key="blockType.type"
                            @click.prevent="addBlock(blockType.type)"
                            class="flex items-center justify-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md transition-colors">
                            <span class="material-icons text-sm mr-1">{{ blockType.icon }}</span>
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
:deep(select) {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
}

:deep(select::-ms-expand) {
    display: none !important;
}

/* Ajout d'icônes Material Design */
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');

/* Animation pour le menu déroulant */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
