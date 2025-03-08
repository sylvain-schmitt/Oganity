<script setup>
import { ref, watch } from 'vue'
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
    modelValue: Object
})

const emit = defineEmits(['update:modelValue'])

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
const handleContentChange = () => {
    props.clearError('content');
}

// Méthode pour mettre à jour le contenu et les styles depuis l'éditeur
const updateContent = (newContent) => {
    localForm.value.content = newContent.content;
    localForm.value.styles = newContent.styles;

    // Mettre à jour directement le formulaire aussi
    props.form.content = newContent.content;
    props.form.styles = newContent.styles;

    emit("update:modelValue", localForm.value);
}

// Méthode pour mettre à jour les champs de base
const updateField = (field, value) => {
    localForm.value[field] = value;
    props.form[field] = value;
    props.clearError(field);
    emit("update:modelValue", localForm.value);
}
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
        <PageEditor v-model="localForm" @update:modelValue="updateContent" @content-change="handleContentChange" />

        <InputError :message="form.errors.content" class="mt-2" />
    </div>
</template>
