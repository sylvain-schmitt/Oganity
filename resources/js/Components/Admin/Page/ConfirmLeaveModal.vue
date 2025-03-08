<script setup>
import Modal from '@/Components/Modal.vue'

defineProps({
    show: Boolean
})

const emit = defineEmits(['close', 'confirm'])
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Abandonner les modifications ?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter cette page ?
            </p>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50"
                    @click="$emit('close')">
                    Continuer l'édition
                </button>
                <button type="button"
                    class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                    @click="$emit('confirm')">
                    Abandonner
                </button>
            </div>
        </div>
    </Modal>
</template>