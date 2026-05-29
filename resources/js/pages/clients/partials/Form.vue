<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    client?: any;
    enums?: any;
    teamSlug: string;
}>();

const emit = defineEmits(['cancel', 'success']);

const form = useForm({
    id: props.client?.id ?? 0, // Usado apenas no modo Lote
    nome: props.client?.nome ?? '',
    tipo: props.client?.tipo ?? '',
    status: props.client?.enderecosIds ?? [],
});

const submit = () => {
    form.put(`/${props.teamSlug}/clientes`, {
        preserveScroll: true,
        onSuccess: () => emit('success'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="form-wrapper">
        <div>
            <label for="nome" class="form-label">
                Nome do Item
                <span class="form-required-asterisk">*</span>
            </label>
            <input
                id="nome"
                type="text"
                class="form-input"
                v-model="form.nome"
                placeholder="Lucas da Silva"
                :autofocus="true"
            />
            <InputError :message="form.errors.nome" class="mt-2" />
        </div>
    </form>
</template>
