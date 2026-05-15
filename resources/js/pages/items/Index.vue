<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'; // Importado usePage
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import itemsRoutes from '@/routes/items/index'; // Renomeado para evitar conflito com a variável 'items'
import type { Team } from '@/types';

interface Item {
    id: number;
    nome: string;
    tipo: string;
    status: string;
}

const props = defineProps<{
    estoque: Item[];
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Estoque',
                href: pageProps.currentTeam?.slug
                    ? itemsRoutes.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});

const page = usePage();
const teamSlug = computed(() => page.props.currentTeam?.slug);
</script>

<template>
    <Head title="Estoque" />

    <div class="flex flex-col space-y-8 p-4">
        <div
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <Heading
                variant="small"
                title="Gerenciar Estoque"
                description="Visualize todos os itens disponíveis no seu time."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/itens/criar`">
                    Adicionar Novo Item
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
                            <th class="px-6 py-3 font-medium">Nome do Item</th>
                            <th class="px-6 py-3 font-medium">Tipo</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-if="props.estoque.length === 0">
                            <td
                                colspan="3"
                                class="px-6 py-8 text-center text-gray-500"
                            >
                                Nenhum item cadastrado no estoque ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="item in props.estoque"
                            :key="item.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                        >
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{ item.nome }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ item.tipo }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ item.status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
