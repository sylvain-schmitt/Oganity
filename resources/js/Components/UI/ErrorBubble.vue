<script setup>
const props = defineProps({
    count: {
        type: Number,
        required: true
    }
})
</script>

<template>
    <div v-if="count > 0"
        class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
        {{ count }}
    </div>
</template>