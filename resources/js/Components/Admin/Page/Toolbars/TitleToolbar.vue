<script setup>
const props = defineProps({
    blockId: String,
    styles: Object,
    hasH1: Boolean
});

const emit = defineEmits(['update']);

const updateStyles = (newStyles) => {
    emit('update', props.blockId, newStyles);
};
</script>

<template>
    <div class="flex flex-wrap gap-4">
        <!-- Alignement -->
        <div class="toolbar-group">
            <div class="toolbar-label">Alignement</div>
            <div class="toolbar-controls">
                <div class="flex bg-white rounded-md shadow-sm border border-gray-300">
                    <button type="button" v-for="option in [
                        { value: 'left', icon: 'format_align_left', label: 'Gauche' },
                        { value: 'center', icon: 'format_align_center', label: 'Centre' },
                        { value: 'right', icon: 'format_align_right', label: 'Droite' }
                    ]" :key="option.value" @click="updateStyles({ textAlign: option.value })" :class="[
                        'p-2 transition-colors',
                        styles.textAlign === option.value
                            ? 'bg-blue-500 text-white'
                            : 'hover:bg-gray-100 text-gray-700',
                        option.value === 'left' ? 'rounded-l-md' : '',
                        option.value === 'right' ? 'rounded-r-md' : ''
                    ]" :title="option.label">
                        <span class="material-icons text-sm">{{ option.icon }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Type de balise -->
        <div class="toolbar-group">
            <div class="toolbar-label">Type</div>
            <div class="relative">
                <select v-model="styles.tag" @change="updateStyles({ tag: styles.tag })"
                    class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option v-for="option in [
                        { value: 'h1', label: 'H1 - Titre principal' },
                        { value: 'h2', label: 'H2 - Sous-titre' },
                        { value: 'h3', label: 'H3 - Titre tertiaire' },
                        { value: 'h4', label: 'H4 - Titre quaternaire' },
                        { value: 'h5', label: 'H5 - Petit titre' },
                        { value: 'h6', label: 'H6 - Mini titre' }
                    ]" :key="option.value" :value="option.value"
                        :disabled="option.value === 'h1' && hasH1 && styles.tag !== 'h1'">
                        {{ option.label }}
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <span class="material-icons text-sm">expand_more</span>
                </div>
            </div>

            <!-- Message d'information pour H1 -->
            <div v-if="styles.tag === 'h1'" class="mt-1 text-xs text-blue-600">
                <span class="material-icons text-xs align-text-bottom">info</span>
                Titre principal (un seul par page)
            </div>

            <!-- Message d'avertissement si H1 existe ailleurs -->
            <div v-if="hasH1 && styles.tag !== 'h1'" class="mt-1 text-xs text-amber-600">
                <span class="material-icons text-xs align-text-bottom">warning</span>
                H1 déjà utilisé ailleurs dans la page
            </div>
        </div>

        <!-- Couleur -->
        <div class="toolbar-group">
            <div class="toolbar-label">Couleur</div>
            <div class="toolbar-controls">
                <div class="flex items-center bg-white rounded-md shadow-sm border border-gray-300 p-1">
                    <div class="w-6 h-6 mr-2 rounded-sm border border-gray-300"
                        :style="{ backgroundColor: styles.color }"></div>
                    <input type="color" v-model="styles.color" @input="updateStyles({ color: styles.color })"
                        class="w-0 h-0 opacity-0 absolute" :id="`color-picker-${blockId}`" />
                    <label :for="`color-picker-${blockId}`"
                        class="text-xs text-gray-600 cursor-pointer hover:text-blue-500 transition-colors">
                        {{ styles.color.toUpperCase() }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Taille -->
        <div class="toolbar-group">
            <div class="toolbar-label">Taille</div>
            <div class="toolbar-controls">
                <div class="relative">
                    <select v-model="styles.fontSize" @change="updateStyles({ fontSize: styles.fontSize })"
                        class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="option in [
                            { value: 18, label: 'Petit' },
                            { value: 24, label: 'Normal' },
                            { value: 32, label: 'Grand' },
                            { value: 40, label: 'Très grand' },
                            { value: 48, label: 'Énorme' }
                        ]" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <span class="material-icons text-sm">expand_more</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
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
</style>