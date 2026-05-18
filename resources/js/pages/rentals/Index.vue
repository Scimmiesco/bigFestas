<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';

// Adapte a importação da rota conforme o Wayfinder gerou para as locações
// Exemplo: import rentalsRoutes from '@/routes/rentals/index';

interface Rental {
    id: number;
    cliente_nome: string;
    endereco_entrega: string;
    data_entrega: string;
    data_recolha: string;
    status: string; // agendado, em_andamento, concluido, cancelado
}

const props = defineProps<{
    locacoes: Rental[];
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Locações',
                // Substitua '#' pela rota correta do Wayfinder, ex: rentalsRoutes.index.url(pageProps.currentTeam.slug)
                href: '#',
            },
        ],
    }),
});

const page = usePage();
const teamSlug = computed(() => page.props.currentTeam?.slug);

// Função simples para estilizar o status da locação
const getStatusClass = (status: string) => {
    switch (status.toLowerCase()) {
        case 'agendado':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-800';
        case 'em_andamento':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800';
        case 'concluido':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
        case 'cancelado':
            return 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300 border-rose-200 dark:border-rose-800';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
    }
};

const formatStatus = (status: string) => {
    return status.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};
</script>

<template>
    <Head title="Locações" />

    <div class="flex flex-col space-y-8 p-4">
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <Heading
                variant="small"
                title="Gerenciar Locações"
                description="Visualize e gerencie todos os agendamentos e aluguéis ativos."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/locacoes/criar`">
                    Nova Locação
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
                            <th class="px-6 py-3 font-medium">Cliente</th>
                            <th
                                class="hidden px-6 py-3 font-medium md:table-cell"
                            >
                                Endereço
                            </th>
                            <th class="px-6 py-3 font-medium">Data Entrega</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-if="props.locacoes.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-8 text-center text-gray-500"
                            >
                                Nenhuma locação registrada ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="locacao in props.locacoes"
                            :key="locacao.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{ locacao.cliente_nome }}
                            </td>
                            <td
                                class="hidden max-w-[200px] truncate px-6 py-4 text-gray-500 md:table-cell dark:text-gray-400"
                                :title="locacao.endereco_entrega"
                            >
                                {{ locacao.endereco_entrega }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{
                                    new Date(
                                        locacao.data_entrega,
                                    ).toLocaleDateString('pt-BR', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    })
                                }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold"
                                    :class="getStatusClass(locacao.status)"
                                >
                                    {{ formatStatus(locacao.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link
                                    :href="`/${teamSlug}/locacoes/${locacao.id}`"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    Detalhes
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
