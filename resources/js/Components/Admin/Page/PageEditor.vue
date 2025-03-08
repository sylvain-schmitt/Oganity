<script setup>
import { ref, computed, watch, provide, nextTick } from 'vue';
import draggable from 'vuedraggable';
import TitleBlock from './Blocks/TitleBlock.vue';
// Importez d'autres types de blocs si nécessaire

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
    // Ajoutez d'autres types de blocs ici
    {
        type: 'text',
        label: 'Texte',
        icon: 'text_fields',
        description: 'Ajouter un paragraphe de texte',
        disabled: true
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
const updateBlock = (blockId, newContent) => {
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
        styles.value[blockId] = {
            color: "#000000",
            textAlign: "left",
            fontSize: 24,
            tag: 'h2'
        };
    }

    // Mettre à jour les styles avec les nouvelles valeurs
    styles.value[blockId] = {
        ...styles.value[blockId],
        ...newStyles
    };

    emitUpdate();
};

// Supprimer un bloc
const removeBlock = (blockId) => {
    content.value = content.value.filter(block => block.id !== blockId);
    delete styles.value[blockId];
    activeBlockId.value = null;
    emitUpdate();
};

// Gérer le changement d'ordre des blocs
const handleOrderChange = () => {
    emitUpdate();
};

// Sélectionner un bloc pour édition
const selectBlock = (blockId) => {
    activeBlockId.value = blockId;
};

// Désélectionner le bloc actif
const deselectBlock = () => {
    activeBlockId.value = null;
};

// Émettre les mises à jour vers le parent
const emitUpdate = () => {
    emit('update:modelValue', {
        content: content.value,
        styles: styles.value
    });
    emit('content-change');
};

// Options pour le drag and drop
const dragOptions = {
    animation: 200,
    group: "blocks",
    disabled: false,
    ghostClass: "ghost-block",
    handle: ".drag-handle"
};

// Fonction pour rendre un bloc selon son type (en mode aperçu)
const renderBlock = (block) => {
    if (!block || !block.type) return null;

    const blockStyles = styles.value[block.id] || {};

    // S'assurer que les styles ont une balise par défaut
    if (block.type === 'title' && !blockStyles.tag) {
        blockStyles.tag = 'h2';
    }

    switch (block.type) {
        case 'title':
            return {
                component: TitleBlock,
                props: {
                    modelValue: block.content,
                    styles: blockStyles,
                    blockId: block.id,
                    readonly: activeBlockId.value !== block.id
                }
            };
        // Ajouter d'autres types de blocs ici
        default:
            return null;
    }
};
</script>

<template>
    <div class="page-editor">
        <!-- Barre d'outils pour ajouter des blocs -->
        <div class="editor-toolbar bg-white p-6 border rounded-lg mb-6 shadow-sm">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Ajouter des éléments</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button v-for="blockType in blockTypes" :key="blockType.type" type="button"
                    @click="addBlock(blockType.type)" :class="[
                        'flex flex-col items-center justify-center p-4 rounded-lg transition-all duration-200 border',
                        blockType.disabled
                            ? 'bg-gray-50 text-gray-400 cursor-not-allowed border-gray-200'
                            : 'bg-white hover:bg-blue-50 hover:border-blue-200 text-gray-700 hover:text-blue-600 border-gray-200 hover:shadow-md'
                    ]" :disabled="blockType.disabled">
                    <span class="material-icons text-2xl mb-2" :class="{ 'text-gray-300': blockType.disabled }">
                        {{ blockType.icon }}
                    </span>
                    <span class="font-medium">{{ blockType.label }}</span>
                    <span class="text-xs text-gray-500 mt-1 text-center" v-if="blockType.description">
                        {{ blockType.description }}
                    </span>
                    <span class="text-xs text-gray-400 mt-1" v-if="blockType.disabled">
                        Bientôt disponible
                    </span>
                </button>
            </div>
        </div>

        <!-- Zone d'édition avec aperçu global -->
        <div class="editor-content bg-white p-6 border rounded-lg min-h-[300px] shadow-sm">
            <div v-if="content.length === 0"
                class="flex flex-col items-center justify-center text-center text-gray-500 py-16">
                <span class="material-icons text-4xl mb-3 text-gray-300">article</span>
                <p class="text-lg">Aucun contenu</p>
                <p class="text-sm mt-1">Utilisez la barre d'outils ci-dessus pour ajouter des blocs de contenu</p>
            </div>

            <div v-else class="relative">
                <!-- Overlay pour désélectionner en cliquant en dehors -->
                <div v-if="activeBlockId" class="fixed inset-0 bg-transparent z-10" @click="deselectBlock"></div>

                <!-- Conteneur des blocs -->
                <draggable v-model="content" v-bind="dragOptions" @change="handleOrderChange" item-key="id"
                    class="space-y-6">
                    <template #item="{ element: block }">
                        <div :class="[
                            'editor-block relative group transition-all duration-200',
                            activeBlockId === block.id ? 'z-20' : 'z-0'
                        ]">
                            <!-- Bloc de contenu avec aperçu -->
                            <div :class="[
                                'block-content relative rounded-lg transition-all duration-200 p-2',
                                activeBlockId === block.id
                                    ? 'ring-2 ring-blue-500 bg-blue-50'
                                    : 'hover:bg-gray-50 cursor-pointer'
                            ]" @click="selectBlock(block.id)">
                                <!-- Poignée de déplacement (visible au survol) -->
                                <div
                                    class="absolute -left-8 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="drag-handle p-1 rounded-full bg-gray-200 hover:bg-gray-300 cursor-move">
                                        <span class="material-icons text-gray-600 text-sm">drag_indicator</span>
                                    </div>
                                </div>

                                <!-- Contenu du bloc (aperçu) -->
                                <component v-if="renderBlock(block)" :is="renderBlock(block).component"
                                    v-bind="renderBlock(block).props"
                                    @update:modelValue="newContent => updateBlock(block.id, newContent)"
                                    @update:styles="newStyles => updateBlockStyles(block.id, newStyles)" />
                            </div>

                            <!-- Barre d'outils contextuelle (visible uniquement pour le bloc actif) -->
                            <div v-if="activeBlockId === block.id"
                                class="block-toolbar bg-white border rounded-lg shadow-md p-2 mt-2 z-30">
                                <!-- En-tête de la barre d'outils -->
                                <div class="flex items-center justify-between border-b pb-2 mb-2">
                                    <div class="flex items-center">
                                        <span class="material-icons text-gray-500 mr-2 text-sm">
                                            {{ block.type === 'title' ? 'title' : 'block' }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ block.type === 'title' ? 'Modifier le titre' : block.type }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <!-- Bouton de suppression -->
                                        <button @click="removeBlock(block.id)" type="button"
                                            class="p-1 rounded text-red-500 hover:bg-red-50">
                                            <span class="material-icons text-sm">delete</span>
                                        </button>
                                        <!-- Bouton pour fermer la barre d'outils -->
                                        <button @click="deselectBlock" type="button"
                                            class="p-1 rounded text-gray-500 hover:bg-gray-100">
                                            <span class="material-icons text-sm">close</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Contenu de la barre d'outils (spécifique au type de bloc) -->
                                <div v-if="block.type === 'title'" class="title-toolbar">
                                    <!-- Champ de saisie du titre -->
                                    <div class="mb-3">
                                        <input v-model="block.content.content"
                                            @input="updateBlock(block.id, block.content)"
                                            placeholder="Saisissez votre titre ici..."
                                            class="w-full p-2 text-lg border border-gray-300 rounded-md focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                                    </div>

                                    <!-- Options de style pour le titre -->
                                    <div class="flex flex-wrap items-center gap-4">
                                        <!-- Type de balise -->
                                        <div class="toolbar-group">
                                            <div class="toolbar-label">Type</div>
                                            <div class="relative">
                                                <select v-model="styles[block.id].tag"
                                                    @change="updateBlockStyles(block.id, { tag: styles[block.id].tag })"
                                                    class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                                    <option v-for="option in [
                                                        { value: 'h1', label: 'H1 - Titre principal' },
                                                        { value: 'h2', label: 'H2 - Sous-titre' },
                                                        { value: 'h3', label: 'H3 - Titre tertiaire' },
                                                        { value: 'h4', label: 'H4 - Titre quaternaire' },
                                                        { value: 'h5', label: 'H5 - Petit titre' },
                                                        { value: 'h6', label: 'H6 - Mini titre' }
                                                    ]" :key="option.value" :value="option.value"
                                                        :disabled="option.value === 'h1' && hasH1 && styles[block.id].tag !== 'h1'">
                                                        {{ option.label }}
                                                    </option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                    <span class="material-icons text-sm">expand_more</span>
                                                </div>
                                            </div>

                                            <!-- Message d'information pour H1 -->
                                            <div v-if="styles[block.id].tag === 'h1'"
                                                class="mt-1 text-xs text-blue-600">
                                                <span class="material-icons text-xs align-text-bottom">info</span>
                                                Titre principal (un seul par page)
                                            </div>

                                            <!-- Message d'avertissement si H1 existe ailleurs -->
                                            <div v-if="hasH1 && styles[block.id].tag !== 'h1'"
                                                class="mt-1 text-xs text-amber-600">
                                                <span class="material-icons text-xs align-text-bottom">warning</span>
                                                H1 déjà utilisé ailleurs dans la page
                                            </div>
                                        </div>

                                        <!-- Alignement -->
                                        <div class="toolbar-group">
                                            <div class="toolbar-label">Alignement</div>
                                            <div class="flex bg-white rounded-md shadow-sm border border-gray-300">
                                                <button type="button" v-for="option in [
                                                    { value: 'left', icon: 'format_align_left', label: 'Gauche' },
                                                    { value: 'center', icon: 'format_align_center', label: 'Centre' },
                                                    { value: 'right', icon: 'format_align_right', label: 'Droite' }
                                                ]" :key="option.value"
                                                    @click="updateBlockStyles(block.id, { textAlign: option.value })"
                                                    :class="[
                                                        'p-2 transition-colors',
                                                        styles[block.id].textAlign === option.value
                                                            ? 'bg-blue-500 text-white'
                                                            : 'hover:bg-gray-100 text-gray-700',
                                                        option.value === 'left' ? 'rounded-l-md' : '',
                                                        option.value === 'right' ? 'rounded-r-md' : ''
                                                    ]" :title="option.label">
                                                    <span class="material-icons text-sm">{{ option.icon }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Couleur -->
                                        <div class="toolbar-group">
                                            <div class="toolbar-label">Couleur</div>
                                            <div
                                                class="flex items-center bg-white rounded-md shadow-sm border border-gray-300 p-1">
                                                <div class="w-6 h-6 mr-2 rounded-sm border border-gray-300"
                                                    :style="{ backgroundColor: styles[block.id].color }"></div>
                                                <input type="color" v-model="styles[block.id].color"
                                                    @input="updateBlockStyles(block.id, { color: styles[block.id].color })"
                                                    class="w-0 h-0 opacity-0 absolute"
                                                    :id="`color-picker-${block.id}`" />
                                                <label :for="`color-picker-${block.id}`"
                                                    class="text-xs text-gray-600 cursor-pointer hover:text-blue-500 transition-colors">
                                                    {{ styles[block.id].color.toUpperCase() }}
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Taille -->
                                        <div class="toolbar-group">
                                            <div class="toolbar-label">Taille</div>
                                            <div class="relative">
                                                <select v-model="styles[block.id].fontSize"
                                                    @change="updateBlockStyles(block.id, { fontSize: styles[block.id].fontSize })"
                                                    class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                                    <option v-for="option in [
                                                        { value: 16, label: 'Petit' },
                                                        { value: 24, label: 'Normal' },
                                                        { value: 32, label: 'Grand' },
                                                        { value: 40, label: 'Très grand' },
                                                        { value: 48, label: 'Énorme' }
                                                    ]" :key="option.value" :value="option.value">
                                                        {{ option.label }}
                                                    </option>
                                                </select>
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                                    <span class="material-icons text-sm">expand_more</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Style (gras/normal) -->
                                        <div class="toolbar-group">
                                            <div class="toolbar-label">Style</div>
                                            <div class="flex bg-white rounded-md shadow-sm border border-gray-300">
                                                <button type="button"
                                                    @click="updateBlockStyles(block.id, { fontWeight: 'normal' })"
                                                    :class="[
                                                        'px-3 py-1.5 text-sm transition-colors rounded-l-md',
                                                        styles[block.id].fontWeight === 'normal'
                                                            ? 'bg-blue-500 text-white'
                                                            : 'hover:bg-gray-100 text-gray-700'
                                                    ]">
                                                    Normal
                                                </button>
                                                <button type="button"
                                                    @click="updateBlockStyles(block.id, { fontWeight: 'bold' })" :class="[
                                                        'px-3 py-1.5 text-sm transition-colors rounded-r-md font-bold',
                                                        styles[block.id].fontWeight === 'bold'
                                                            ? 'bg-blue-500 text-white'
                                                            : 'hover:bg-gray-100 text-gray-700'
                                                    ]">
                                                    Gras
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ajouter d'autres types de barres d'outils ici -->
                            </div>
                        </div>
                    </template>
                </draggable>
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
