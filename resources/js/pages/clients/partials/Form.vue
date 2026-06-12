<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { Save, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import clientsRoutes from '@/routes/clients';

const props = defineProps<{
    client?: any;
    enums?: any;
    teamSlug: string;
}>();

const emit = defineEmits(['cancel', 'success']);

const isEditing = Boolean(props.client && props.client.id);

const form = useForm({
    id: props.client?.id ?? 0,
    nome: props.client?.nome ?? '',
    natureza_juridica: props.client?.natureza_juridica ?? '',
    cpf_cnpj: props.client?.cpf_cnpj ?? '',
});

const submit = () => {
    if (isEditing) {
        form.put(`/${props.teamSlug}/clientes/${props.client.id}`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        });
    } else {
        form.post(`/${props.teamSlug}/clientes`, {
            preserveScroll: true,
            onSuccess: () => emit('success'),
        });
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="form-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label for="nome" class="form-label">
                    Nome do Cliente
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="nome"
                    type="text"
                    class="form-input"
                    v-model="form.nome"
                    placeholder="Nome completo ou Razão Social"
                    :autofocus="true"
                    :required="true"
                />
                <InputError :message="form.errors.nome" />
            </div>

            <div>
                <label for="natureza_juridica" class="form-label">
                    Natureza Jurídica
                    <span class="form-required-asterisk">*</span>
                </label>
                <select
                    id="natureza_juridica"
                    v-model="form.natureza_juridica"
                    class="form-input"
                    :required="true"
                >
                    <option :disabled="true" :selected="true" value="">
                        Selecione uma opção
                    </option>
                    <option
                        v-for="tipo in enums?.client_types"
                        :key="tipo.value"
                        :value="tipo.value"
                    >
                        {{ tipo.label }}
                    </option>
                </select>
                <InputError :message="form.errors.natureza_juridica" />
            </div>

            <div>
                <label for="cpf_cnpj" class="form-label">
                    CPF / CNPJ
                    <span class="form-required-asterisk">*</span>
                </label>
                <input
                    id="cpf_cnpj"
                    type="text"
                    class="form-input"
                    v-model="form.cpf_cnpj"
                    placeholder="000.000.000-00"
                    :required="true"
                />
                <InputError :message="form.errors.cpf_cnpj" />
            </div>
        </div>

        <div class="form-actions mt-6">
            <Button variant="outline" as-child>
                <Link :href="clientsRoutes.index(teamSlug)" class="gap-2">
                    <X class="h-4 w-4" /> Cancelar
                </Link>
            </Button>
            <Button
                variant="default"
                type="submit"
                :disabled="form.processing"
                class="gap-2"
            >
                <Save class="h-4 w-4" />
                {{ form.processing ? 'Salvando...' : 'Salvar Cliente' }}
            </Button>
        </div>
    </form>
</template>
