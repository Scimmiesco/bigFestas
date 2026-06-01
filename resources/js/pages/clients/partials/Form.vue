<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import { MapPinPlus, Save, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import clientsRoutes from '@/routes/clients';

const props = defineProps<{
    client?: any;
    enums?: any;
    teamSlug: string;
}>();

const emit = defineEmits(['cancel', 'success']);

const form = useForm({
    id: props.client?.id ?? 0, // Usado apenas no modo Lote
    nome: props.client?.nome ?? '',
    tipo_cliente: props.client?.tipo ?? '',
    enderecos: props.client?.enderecosIds ?? [],
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
                Nome do Cliente
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
            <InputError :message="form.errors.nome" />
        </div>

        <div>
            <label for="tipo_cliente" class="form-label">
                Tipo de cliente
                <span class="form-required-asterisk">*</span>
            </label>
            <select
                id="tipo_cliente"
                v-model="form.tipo_cliente"
                class="form-input"
                :required="true"
            >
                <option :disabled="true" :selected="true" value="">
                    Selecione uma opção
                </option>
                <option
                    v-for="tipo in enums?.tipo_cliente"
                    v-bind="tipo.value"
                    :key="tipo.value"
                    :value="tipo.value"
                ></option>
            </select>

            <InputError :message="form.errors.tipo_cliente" />
        </div>

        <div>
            <label for="endereco" class="form-label">Endereço</label>
            <div class="flex flex-nowrap items-center justify-center gap-2">
                <select
                    id="endereco"
                    v-model="form.enderecos"
                    class="form-input"
                >
                    <option :disabled="true" :selected="true" value="">
                        Selecione uma opção
                    </option>

                    <option
                        v-for="endereco in client.enderecos"
                        :value="endereco.id"
                        :key="endereco.id"
                    >
                        {{ endereco.lograduro }}
                    </option>
                </select>
                <Button><MapPinPlus /></Button>
            </div>
        </div>

        <div class="form-actions">
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
