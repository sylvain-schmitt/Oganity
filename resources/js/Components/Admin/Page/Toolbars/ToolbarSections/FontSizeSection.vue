<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: [Number, String],
    default: 16
  },
  blockType: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update']);

// Options de taille différentes selon le type de bloc
const options = computed(() => {
  if (props.blockType === 'title') {
    return [
      { value: 18, label: 'Petit' },
      { value: 24, label: 'Normal' },
      { value: 32, label: 'Grand' },
      { value: 40, label: 'Très grand' },
      { value: 48, label: 'Énorme' }
    ];
  } else {
    return [
      { value: 12, label: 'Très petit' },
      { value: 14, label: 'Petit' },
      { value: 16, label: 'Normal' },
      { value: 18, label: 'Grand' },
      { value: 20, label: 'Très grand' }
    ];
  }
});
</script>

<template>
  <div class="toolbar-group">
    <div class="toolbar-label">Taille</div>
    <div class="toolbar-controls">
      <div class="relative">
        <select 
          :value="value" 
          @change="emit('update', parseInt($event.target.value))"
          class="block w-full bg-white rounded-md shadow-sm border border-gray-300 text-sm py-1.5 px-3 pr-8 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option 
            v-for="option in options" 
            :key="option.value" 
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
          <span class="material-icons text-sm">expand_more</span>
        </div>
      </div>
    </div>
  </div>
</template> 