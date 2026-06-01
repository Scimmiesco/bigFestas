<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{ enderecos?: any; enums?: any; teamSlug?: any }>();
const form = useForm({
    id: props.enderecos?.id ?? 0, // Usado apenas no modo Lote
    logradouro: props.enderecos?.nome ?? '',
    numero: props.enderecos?.nome ?? '',
    tipo_cliente: props.enderecos?.tipo ?? '',
    enderecos: props.enderecos?.enderecosIds ?? [],
});
</script>

<template>
    <form class="form-wrapper">
        <div>
            <label class="form-label" for="logradouro">Logradouro</label>
            <input
                type="text"
                autofocus="true"
                placeholder="Rua dos bobos"
                class="form-input"
                v-model="form.logradouro"
                id="logradouro"
            />
        </div>

        <div>
            <label class="form-label" for="numero">Logradouro</label>
            <input
                type="text"
                autofocus="true"
                placeholder="000"
                class="form-input"
                v-model="form.numero"
                id="numero"
            />
        </div>

        <div>
            <label class="form-label" for="observacoes">Observações</label>
            <textarea
                type="text"
                autofocus="true"
                placeholder="Apenas bater palma..."
                class="form-input"
                v-model="form.numero"
                id="logradouro"
            />
        </div>
    </form>
</template>
