<script setup>
const props = defineProps({
  value: {
    type: String,
    default: '#000000'
  },
  blockId: String
});

const emit = defineEmits(['update']);
</script>

<template>
  <div class="toolbar-group">
    <div class="toolbar-label">Couleur</div>
    <div class="toolbar-controls">
      <div class="flex items-center bg-white rounded-md shadow-sm border border-gray-300 p-1">
        <div 
          class="w-6 h-6 mr-2 rounded-sm border border-gray-300"
          :style="{ backgroundColor: value }"
        ></div>
        <input 
          type="color" 
          :value="value" 
          @input="emit('update', $event.target.value)"
          class="w-0 h-0 opacity-0 absolute" 
          :id="`color-picker-${blockId}`" 
        />
        <label 
          :for="`color-picker-${blockId}`"
          class="text-xs text-gray-600 cursor-pointer hover:text-blue-500 transition-colors"
        >
          {{ value.toUpperCase() }}
        </label>
      </div>
    </div>
  </div>
</template> 