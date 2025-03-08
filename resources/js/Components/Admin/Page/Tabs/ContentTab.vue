<script setup>
import { ref, watch, computed } from 'vue'
import InputError from '@/Components/InputError.vue'
import PageEditor from '@/Components/Admin/Page/PageEditor.vue'

const props = defineProps({
    form: Object,
    templates: {
        type: Object,
        required: true
    },
    statuses: {
        type: Object,
        required: true
    },
    clearError: {
        type: Function,
        required: true
    },
    modelValue: Object,
    initialData: Object
})

const emit = defineEmits(['update:modelValue', 'content-changed'])

// Initialisation du formulaire local avec les valeurs du modelValue ou des valeurs par défaut
const localForm = ref(props.modelValue || {
    title: "",
    template: "",
    status: "",
    content: [],
    styles: {}
});

// Surveiller les changements du modelValue pour mettre à jour le formulaire local
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        localForm.value = newValue;
    }
}, { deep: true });

// Méthode pour gérer les changements de contenu
const handleContentChange = (newContent) => {
    props.clearError('content');

    // S'assurer que le contenu est correctement mis à jour dans le formulaire
    props.form.content = newContent.content;
    props.form.styles = newContent.styles;

    // Émettre un événement pour indiquer que le contenu a changé
    emit('content-changed', true);
}

// Méthode pour mettre à jour le contenu et les styles depuis l'éditeur
const updateContent = (newContent) => {
    localForm.value.content = newContent.content;
    localForm.value.styles = newContent.styles;

    // Mettre à jour directement le formulaire aussi
    props.form.content = newContent.content;
    props.form.styles = newContent.styles;

    // Effacer l'erreur de contenu si elle existe
    props.clearError('content');

    emit("update:modelValue", localForm.value);
    emit('content-changed', true);
}

// Méthode pour mettre à jour les champs de base
const updateField = (field, value) => {
    localForm.value[field] = value;
    props.form[field] = value;
    props.clearError(field);
    emit("update:modelValue", localForm.value);
}

// Vérifier si le contenu est vide
const isContentEmpty = computed(() => {
    return !props.form.content || props.form.content.length === 0;
});
</script>

<template>
    <div class="space-y-6">
        <!-- Informations de base -->
        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="grid grid-cols-1 gap-6">
                <!-- Titre -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Titre de la page
                    </label>
                    <input type="text" id="title" v-model="form.title"
                        @input="updateField('title', $event.target.value)"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.title }" required />
                    <InputError :message="form.errors.title" class="mt-2" />
                </div>

                <!-- Template et Statut -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="template" class="block text-sm font-medium text-gray-700">
                            Template
                        </label>
                        <select id="template" v-model="form.template"
                            @change="updateField('template', $event.target.value)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.template }">
                            <option v-for="(label, value) in templates" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.template" class="mt-2" />
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Statut
                        </label>
                        <select id="status" v-model="form.status" @change="updateField('status', $event.target.value)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            :class="{ 'border-red-500': form.errors.status }">
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Éditeur de page -->
        <div :class="{ 'border-red-500 ring-1 ring-red-500': form.errors.content }">
            <PageEditor v-model="localForm" @update:modelValue="updateContent" @content-change="handleContentChange" />
        </div>

        <InputError :message="form.errors.content" class="mt-2" />

        <!-- Message d'avertissement si le contenu est vide -->
        <div v-if="isContentEmpty" class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Le contenu de la page est vide. Veuillez ajouter au moins un bloc de contenu.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
