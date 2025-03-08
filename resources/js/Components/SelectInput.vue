<script setup>
import { onMounted, ref } from 'vue';

const input = ref(null);

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: [Object, Array],
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
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
    <select ref="input" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue" :disabled="disabled" @change="$emit('update:modelValue', $event.target.value)">
        <template v-if="Array.isArray(options)">
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </template>
        <template v-else>
            <option v-for="(label, value) in options" :key="value" :value="value">
                {{ label }}
            </option>
        </template>
    </select>
</template>