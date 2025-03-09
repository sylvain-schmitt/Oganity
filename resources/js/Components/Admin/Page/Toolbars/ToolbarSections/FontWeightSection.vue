<script setup>
const props = defineProps({
  value: {
    type: String,
    default: 'normal'
  }
});

const emit = defineEmits(['update']);
</script>

<template>
  <div class="toolbar-group">
    <div class="toolbar-label">Style</div>
    <div class="toolbar-controls">
      <div class="flex bg-white rounded-md shadow-sm border border-gray-300">
        <button 
          type="button" 
          @click="emit('update', 'normal')" 
          :class="[
            'px-3 py-1.5 text-sm transition-colors rounded-l-md',
            value === 'normal'
              ? 'bg-blue-500 text-white'
              : 'hover:bg-gray-100 text-gray-700'
          ]"
        >
          Normal
        </button>
        <button 
          type="button" 
          @click="emit('update', 'bold')" 
          :class="[
            'px-3 py-1.5 text-sm transition-colors rounded-r-md font-bold',
            value === 'bold'
              ? 'bg-blue-500 text-white'
              : 'hover:bg-gray-100 text-gray-700'
          ]"
        >
          Gras
        </button>
      </div>
    </div>
  </div>
</template> 