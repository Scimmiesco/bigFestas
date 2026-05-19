<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, CheckSquare } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import itemsRoutes from '@/routes/items/index';
import type { Team } from '@/types';

// Importando o formulário unificado
import ItemForm from './Partials/Form.vue';

interface Item {
    id: number;
    nome: string;
    tipo: string;
    status: string;
}

interface PaginatedData {
    data: Item[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    estoque: PaginatedData;
    enums?: any; // Recebendo enums para passar pro ItemForm
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

// --- LÓGICA DE SELEÇÃO EM LOTE ---
const selectedItems = ref<number[]>([]);
// Estado que controla se o formulário de edição em lote está visível
const isBulkEditing = ref(false);

const toggleSelectAll = (event: Event) => {
    const isChecked = (event.target as HTMLInputElement).checked;
    if (isChecked) {
        selectedItems.value = props.estoque.data.map((item) => item.id);
    } else {
        selectedItems.value = [];
    }
};

const isAllSelected = computed(() => {
    return (
        props.estoque.data.length > 0 &&
        selectedItems.value.length === props.estoque.data.length
    );
});

// Ação executada com sucesso pelo ItemForm
const handleBulkSuccess = () => {
    isBulkEditing.value = false;
    selectedItems.value = [];
};

// --- MÉTODO DE EXCLUSÃO INDIVIDUAL ---
const deletar = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este item permanentemente?')) {
        router.delete(`/${teamSlug.value}/itens/${id}`, {
            preserveScroll: true,
            // Opcional: limpar seleção se o item excluído estava selecionado
            onSuccess: () => {
                selectedItems.value = selectedItems.value.filter(
                    (itemId) => itemId !== id,
                );
            },
        });
    }
};
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
                <Link
                    :href="`/${teamSlug ?? ''}/itens/criar`"
                    class="flex items-center gap-2"
                >
                    <Plus class="h-4 w-4" />
                    Adicionar Novo Item
                </Link>
            </Button>
        </div>

        <div
            v-if="selectedItems.length > 0 && !isBulkEditing"
            class="flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 shadow-sm dark:border-blue-900/50 dark:bg-blue-900/20"
        >
            <div
                class="flex items-center gap-2 text-sm font-medium text-blue-700 dark:text-blue-300"
            >
                <CheckSquare class="h-5 w-5" />
                <span>{{ selectedItems.length }} item(ns) selecionado(s)</span>
            </div>

            <div class="flex items-center gap-3">
                <Button
                    @click="isBulkEditing = true"
                    variant="outline"
                    size="sm"
                    class="flex h-9 items-center gap-2"
                >
                    <Pencil class="h-4 w-4" />
                    Editar Itens Selecionados
                </Button>
            </div>
        </div>

        <div
            v-if="isBulkEditing"
            class="animate-in fade-in slide-in-from-top-4"
        >
            <Heading
                variant="small"
                title="Edição Múltipla"
                description="Os campos alterados abaixo serão aplicados em todos os itens selecionados."
            />

            <ItemForm
                :item-ids="selectedItems"
                :enums="props.enums"
                :team-slug="teamSlug as string"
                @cancel="isBulkEditing = false"
                @success="handleBulkSuccess"
            />
        </div>

        <div
            v-show="!isBulkEditing"
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="border-b bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300"
                    >
                        <tr>
                            <th class="w-12 px-6 py-3">
                                <input
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleSelectAll"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                />
                            </th>
                            <th class="px-6 py-3 font-medium">Nome do Item</th>
                            <th class="px-6 py-3 font-medium">Tipo</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 text-right font-medium">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr v-if="props.estoque.data.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-8 text-center text-gray-500"
                            >
                                Nenhum item cadastrado no estoque ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="item in props.estoque.data"
                            :key="item.id"
                            class="group transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50"
                            :class="{
                                'bg-blue-50/50 dark:bg-blue-900/10':
                                    selectedItems.includes(item.id),
                            }"
                        >
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    v-model="selectedItems"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                />
                            </td>
                            <td
                                class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{ item.nome }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ item.tipo }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="
                                        item.status === 'ativo'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    "
                                >
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div
                                    class="flex justify-end gap-3 opacity-100 transition-opacity group-hover:opacity-100 sm:opacity-0"
                                >
                                    <Link
                                        :href="`/${teamSlug}/itens/${item.id}/editar`"
                                        class="flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        <Pencil class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Editar</span
                                        >
                                    </Link>

                                    <button
                                        @click="deletar(item.id)"
                                        type="button"
                                        class="flex items-center gap-1 text-sm font-medium text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Excluir</span
                                        >
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 dark:border-gray-700 dark:bg-gray-800"
                v-if="props.estoque.links.length > 3"
            >
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        :href="props.estoque.links[0].url ?? '#'"
                        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Anterior
                    </Link>
                    <Link
                        :href="
                            props.estoque.links[props.estoque.links.length - 1]
                                .url ?? '#'
                        "
                        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Próxima
                    </Link>
                </div>
                <div
                    class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            Página
                            <span class="font-medium">{{
                                props.estoque.current_page
                            }}</span>
                            de
                            <span class="font-medium">{{
                                props.estoque.last_page
                            }}</span>
                        </p>
                    </div>
                    <div>
                        <nav
                            class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                            aria-label="Pagination"
                        >
                            <template
                                v-for="(link, index) in props.estoque.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-gray-300 ring-inset hover:bg-gray-50 dark:ring-gray-700 dark:hover:bg-gray-700"
                                    :class="
                                        link.active
                                            ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                                            : 'text-gray-900 dark:text-gray-200'
                                    "
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span
                                    v-else
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 ring-1 ring-gray-300 ring-inset dark:ring-gray-700"
                                >
                                    <span v-html="link.label"></span>
                                </span>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
