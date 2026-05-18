<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import paymentRoutes from '@/routes/payment/index';
import type { Team } from '@/types';

// Recebe as locações enviadas pelo Controller
const props = defineProps<{
    enums: any;
    locacoes: Array<{ id: number; cliente_nome: string }>;
}>();

const page = usePage();
const teamSlug = computed(() => page.props.currentTeam?.slug);

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Pagamentos',
                href: pageProps.currentTeam?.slug
                    ? paymentRoutes.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
            {
                title: 'Novo Pagamento',
                href: '#',
            },
        ],
    }),
});

// Campos baseados no Model Payment
const form = useForm({
    rental_id: '',
    valor: '',
    metodo: 'pix', // Valor padrão sugerido
    status: 'pendente', // Baseado no Enum PaymentStatus
    data_vencimento: '',
    data_pagamento: '',
});

const submit = () => {
    form.post(`/${teamSlug.value}/pagamentos`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Novo Pagamento" />

    <div class="mx-auto max-w-3xl p-4 py-6">
        <Heading
            variant="small"
            title="Registrar Novo Pagamento"
            description="Preencha os detalhes financeiros referentes a uma locação."
        />

        <div
            class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Seção 1: Vínculo e Valor -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="rental_id"
                            >Locação Referente
                            <span class="text-red-500">*</span></Label
                        >
                        <!-- Usando um select estilizado para parecer com os inputs do shadcn -->
                        <select
                            id="rental_id"
                            v-model="form.rental_id"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="" disabled>
                                Selecione uma locação...
                            </option>
                            <option
                                v-for="locacao in props.locacoes"
                                :key="locacao.id"
                                :value="locacao.id"
                            >
                                Locação #{{ locacao.id }} -
                                {{ locacao.cliente_nome }}
                            </option>
                        </select>
                        <InputError :message="form.errors.rental_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="valor"
                            >Valor do Pagamento
                            <span class="text-red-500">*</span></Label
                        >
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 sm:text-sm"
                            >
                                R$
                            </span>
                            <Input
                                id="valor"
                                type="number"
                                step="0.01"
                                min="0"
                                v-model="form.valor"
                                class="pl-10"
                                placeholder="0,00"
                                required
                            />
                        </div>
                        <InputError :message="form.errors.valor" />
                    </div>
                </div>

                <!-- Seção 2: Método e Status -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="metodo_pagamento"
                            >Método de Pagamento</Label
                        >
                        <select
                            id="metodo_pagamento"
                            v-model="form.metodo"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                        >
                            <option value="" disabled selected>
                                Selecione um tipo
                            </option>
                            <option
                                v-for="opcao in enums?.payment_types"
                                :key="opcao.value"
                                :value="opcao.value"
                            >
                                {{ opcao.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.metodo" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="status">Status do Pagamento</Label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                        >
                            <option value="" disabled selected>
                                Selecione um tipo
                            </option>
                            <option
                                v-for="opcao in enums?.payment_status"
                                :key="opcao.value"
                                :value="opcao.value"
                            >
                                {{ opcao.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>
                </div>

                <!-- Seção 3: Datas -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="data_vencimento"
                            >Data de Vencimento
                            <span class="text-red-500">*</span></Label
                        >
                        <Input
                            id="data_vencimento"
                            type="date"
                            v-model="form.data_vencimento"
                            required
                        />
                        <InputError :message="form.errors.data_vencimento" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="data_pagamento"
                            >Data de Pagamento (Realizado)</Label
                        >
                        <Input
                            id="data_pagamento"
                            type="datetime-local"
                            v-model="form.data_pagamento"
                            :disabled="form.status === 'pendente'"
                        />
                        <p class="text-xs text-gray-500">
                            Deixe em branco se ainda não foi pago.
                        </p>
                        <InputError :message="form.errors.data_pagamento" />
                    </div>
                </div>

                <!-- Ações -->
                <div
                    class="mt-6 flex items-center justify-end gap-4 border-t pt-6 dark:border-gray-700"
                >
                    <Button type="button" variant="outline" as-child>
                        <Link :href="`/${teamSlug ?? ''}/pagamentos`"
                            >Cancelar</Link
                        >
                    </Button>

                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing ? 'Salvando...' : 'Salvar Pagamento'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
