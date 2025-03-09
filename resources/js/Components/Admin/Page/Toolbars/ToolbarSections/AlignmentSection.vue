<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: String,
    default: 'left'
  },
  blockType: {
    type: String,
    default: 'title'
  }
});

const emit = defineEmits(['update']);

// Options d'alignement conditionnelles selon le type de bloc
const options = computed(() => {
  // Options de base pour tous les types
  const baseOptions = [
    { value: 'left', icon: 'format_align_left', label: 'Gauche' },
    { value: 'center', icon: 'format_align_center', label: 'Centre' },
    { value: 'right', icon: 'format_align_right', label: 'Droite' }
  ];
  
  // Ajouter l'option "justifié" uniquement pour les blocs de texte
  if (props.blockType === 'text') {
    baseOptions.push({ value: 'justify', icon: 'format_align_justify', label: 'Justifié' });
  }
  
  return baseOptions;
});
</script>

<template>
  <div class="toolbar-group">
    <div class="toolbar-label">Alignement</div>
    <div class="toolbar-controls">
      <div class="flex bg-white rounded-md shadow-sm border border-gray-300">
        <button 
          type="button" 
          v-for="option in options" 
          :key="option.value" 
          @click="emit('update', option.value)" 
          :class="[
            'p-2 transition-colors',
            value === option.value
              ? 'bg-blue-500 text-white'
              : 'hover:bg-gray-100 text-gray-700',
            option.value === 'left' ? 'rounded-l-md' : '',
            option.value === options[options.length-1].value ? 'rounded-r-md' : ''
          ]" 
          :title="option.label"
        >
          <span class="material-icons text-sm">{{ option.icon }}</span>
        </button>
      </div>
    </div>
  </div>
</template> 