<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    }
});

defineEmits(['update:modelValue']);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <textarea ref="input" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue" :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"></textarea>
</template>