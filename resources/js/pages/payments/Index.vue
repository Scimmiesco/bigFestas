<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import paymentRoutes from '@/routes/payment/index';
import type { Team } from '@/types';

const props = defineProps<{
    pagamentos: any;
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Pagamentos',
                href: pageProps.currentTeam?.slug
                    ? paymentRoutes.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});

const page = usePage();
const teamSlug = computed(() => page.props.currentTeam?.slug);

// Formatar Dinheiro (R$)
const formatMoney = (value: number | string | null | undefined) => {
    if (value === null || value === undefined) return 'R$ 0,00';
    const amount = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(amount)) return 'R$ 0,00';

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(amount);
};

// Formatar Data (DD/MM/AAAA)
const formatDate = (value: string | null | undefined, withTime = false) => {
    if (!value) return '-';
    const date = new Date(value);
    if (isNaN(date.getTime())) return value;

    const options: Intl.DateTimeFormatOptions = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        ...(withTime && { hour: '2-digit', minute: '2-digit' }),
    };

    return new Intl.DateTimeFormat('pt-BR', options).format(date);
};
</script>

<template>
    <Head title="Pagamentos" />

    <div class="flex flex-col space-y-8 p-4">
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <Heading
                variant="small"
                title="Gerenciar Pagamentos"
                description="Visualize todos os pagamentos realizados ou pendentes."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/pagamentos/criar`">
                    Adicionar Novo Pagamento
                </Link>
            </Button>
        </div>

        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300"
                    >
                        <tr>
                            <th class="px-6 py-3 font-medium">Locação Ref.</th>
                            <th class="px-6 py-3 font-medium">Valor</th>
                            <th class="px-6 py-3 font-medium">Método</th>
                            <th class="px-6 py-3 font-medium">Vencimento</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-if="props.pagamentos.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-8 text-center text-gray-500"
                            >
                                Nenhum pagamento registrado ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="item in props.pagamentos"
                            :key="item.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                Locação #{{ item.rental_id }}
                                <span
                                    v-if="item.rental"
                                    class="block text-xs font-normal text-gray-500"
                                >
                                    {{ item.rental.cliente_nome }}
                                </span>
                            </td>

                            <td
                                class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400"
                            >
                                {{ formatMoney(item.valor) }}
                            </td>

                            <td
                                class="px-6 py-4 text-gray-600 capitalize dark:text-gray-300"
                            >
                                {{ item.metodo ?? '-' }}
                            </td>

                            <td
                                class="px-6 py-4 text-gray-600 dark:text-gray-300"
                            >
                                {{ formatDate(item.data_vencimento) }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400':
                                            item.status === 'realizado',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400':
                                            item.status === 'pendente',
                                        'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400':
                                            item.status === 'estornado',
                                    }"
                                >
                                    {{ item.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
