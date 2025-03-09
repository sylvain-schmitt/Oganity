<script setup>
import { computed } from 'vue';
import AlignmentSection from './ToolbarSections/AlignmentSection.vue';
import ColorSection from './ToolbarSections/ColorSection.vue';
import FontSizeSection from './ToolbarSections/FontSizeSection.vue';
import FontWeightSection from './ToolbarSections/FontWeightSection.vue';
import HeadingTagSection from './ToolbarSections/HeadingTagSection.vue';

const props = defineProps({
  blockId: String,
  blockType: {
    type: String,
    required: true
  },
  styles: Object,
  hasH1: Boolean
});

const emit = defineEmits(['update']);

// Déterminer quelles sections afficher selon le type de bloc
const sections = computed(() => {
  switch (props.blockType) {
    case 'title':
      return ['alignment', 'tag', 'color', 'fontSize'];
    case 'text':
      return ['alignment', 'color', 'fontSize', 'fontWeight'];
    case 'image':
      return ['alignment', 'border', 'shadow'];
    default:
      return ['alignment', 'color']; // Sections par défaut
  }
});

// Fonction pour mettre à jour les styles
const updateStyles = (newStyles) => {
  emit('update', props.blockId, newStyles);
};
</script>

<template>
  <div class="flex flex-wrap gap-4">
    <!-- Section d'alignement (commune à tous les types) -->
    <AlignmentSection 
      v-if="sections.includes('alignment')" 
      :value="styles.textAlign" 
      :block-type="blockType"
      @update="updateStyles({ textAlign: $event })" 
    />
    
    <!-- Section de type de titre (spécifique aux titres) -->
    <HeadingTagSection 
      v-if="sections.includes('tag')" 
      :value="styles.tag" 
      :has-h1="hasH1" 
      :current-block-id="blockId"
      @update="updateStyles({ tag: $event })" 
    />
    
    <!-- Section de couleur -->
    <ColorSection 
      v-if="sections.includes('color')" 
      :value="styles.color" 
      :block-id="blockId"
      @update="updateStyles({ color: $event })" 
    />
    
    <!-- Section de taille de police -->
    <FontSizeSection 
      v-if="sections.includes('fontSize')" 
      :value="styles.fontSize" 
      :block-type="blockType"
      @update="updateStyles({ fontSize: $event })" 
    />
    
    <!-- Section de graisse de police (pour le texte) -->
    <FontWeightSection 
      v-if="sections.includes('fontWeight')" 
      :value="styles.fontWeight" 
      @update="updateStyles({ fontWeight: $event })" 
    />
  </div>
</template> 