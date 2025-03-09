<script setup>
import { computed, inject } from 'vue';

const props = defineProps({
  value: {
    type: String,
    default: 'h2'
  },
  hasH1: Boolean,
  currentBlockId: String
});

const emit = defineEmits(['update']);

// Injecter les fonctions de vérification H1 si disponibles
const checkH1Exists = inject('checkH1Exists', () => false);
const currentBlockIsH1 = inject('currentBlockIsH1', () => false);

// Déterminer si ce bloc est actuellement un H1
const isCurrentBlockH1 = computed(() => {
  return currentBlockIsH1(props.currentBlockId);
});

// Options disponibles pour les balises de titre
const options = computed(() => {
  return [
    { 
      value: 'h1', 
      label: 'H1 - Titre principal',
      disabled: props.hasH1 && !isCurrentBlockH1.value
    },
    { value: 'h2', label: 'H2 - Sous-titre' },
    { value: 'h3', label: 'H3 - Titre tertiaire' },
    { value: 'h4', label: 'H4 - Titre quaternaire' },
    { value: 'h5', label: 'H5 - Petit titre' },
    { value: 'h6', label: 'H6 - Mini titre' }
  ];
});
</script>

<template>
  <div class="toolbar-group">
    <div class="toolbar-label">Type</div>
    <div class="relative">
      <select 
        :value="value" 
        @change="emit('update', $event.target.value)"
        class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      >
        <option 
          v-for="option in options" 
          :key="option.value" 
          :value="option.value"
          :disabled="option.disabled"
        >
          {{ option.label }}
        </option>
      </select>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
        <span class="material-icons text-sm">expand_more</span>
      </div>
    </div>
    
    <!-- Message d'information pour H1 actuel -->
    <div v-if="value === 'h1'" class="mt-1 text-xs text-blue-600 flex items-center">
      <span class="material-icons text-xs mr-1">info</span>
      Titre principal (un seul par page)
    </div>
    
    <!-- Message d'avertissement pour H1 déjà utilisé -->
    <div v-else-if="hasH1 && !isCurrentBlockH1" class="mt-1 text-xs text-amber-600 flex items-center">
      <span class="material-icons text-xs mr-1">warning</span>
      Un H1 est déjà présent sur cette page
    </div>
  </div>
</template> 