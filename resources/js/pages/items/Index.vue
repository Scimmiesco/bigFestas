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

    <div class="page-container">
        <div class="page-header">
            <Heading
                variant="default"
                title="Gerenciar Estoque"
                description="Visualize todos os itens disponíveis no seu time."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/itens/criar`">
                    <Plus class="h-4 w-4" />
                    Adicionar Novo Item
                </Link>
            </Button>
        </div>

        <div
            v-if="selectedItems.length > 0 && !isBulkEditing"
            class="bulk-action-bar"
        >
            <div class="bulk-info">
                <CheckSquare class="h-5 w-5" />
                <span>{{ selectedItems.length }} item(ns) selecionado(s)</span>
            </div>

            <div class="flex items-center gap-3">
                <Button
                    @click="isBulkEditing = true"
                    variant="outline"
                    size="sm"
                    class="btn-bulk-edit"
                >
                    <Pencil class="h-4 w-4" />
                    Editar Itens Selecionados
                </Button>
            </div>
        </div>

        <div v-if="isBulkEditing" class="bulk-form-container">
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

        <div v-show="!isBulkEditing" class="table-wrapper">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="table-head">
                        <tr>
                            <th class="w-12 p-4">
                                <input
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    @change="toggleSelectAll"
                                    class="checkbox-base"
                                />
                            </th>
                            <th class="th-cell text-nowrap">Nome do Item</th>
                            <th class="th-cell">Tipo</th>
                            <th class="th-cell">Status</th>
                            <th class="th-cell">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-if="props.estoque.data.length === 0">
                            <td colspan="5" class="empty-state">
                                Nenhum item cadastrado no estoque ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="item in props.estoque.data"
                            :key="item.id"
                            class="group table-row"
                            :class="{
                                'saturate-150': selectedItems.includes(item.id),
                            }"
                        >
                            <td class="px-3 py-2 text-center">
                                <input
                                    type="checkbox"
                                    :value="item.id"
                                    v-model="selectedItems"
                                    class="checkbox-base"
                                />
                            </td>
                            <td class="td-cell font-medium">
                                <div class="item-name">
                                    {{ item.nome }}
                                </div>
                            </td>

                            <td class="td-cell text-center font-mono uppercase">
                                {{ item.tipo }}
                            </td>
                            <td
                                class="td-cell text-center font-mono text-xs font-semibold uppercase"
                            >
                                {{ item.status }}
                            </td>
                            <td class="td-cell">
                                <div class="action-buttons">
                                    <Link
                                        :href="`/${teamSlug}/itens/${item.id}/editar`"
                                        class="btn-edit"
                                    >
                                        <Pencil class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Editar</span
                                        >
                                    </Link>

                                    <button
                                        @click="deletar(item.id)"
                                        type="button"
                                        class="btn-delete"
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
                class="pagination-wrapper"
                v-if="props.estoque.links.length > 3"
            >
                <div class="pagination-mobile">
                    <Link
                        :href="props.estoque.links[0].url ?? '#'"
                        class="btn-pagination"
                    >
                        Anterior
                    </Link>
                    <Link
                        :href="
                            props.estoque.links[props.estoque.links.length - 1]
                                .url ?? '#'
                        "
                        class="btn-pagination ml-3"
                    >
                        Próxima
                    </Link>
                </div>

                <div class="pagination-desktop">
                    <div>
                        <p class="pagination-info">
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
                        <nav class="pagination-nav" aria-label="Pagination">
                            <template
                                v-for="(link, index) in props.estoque.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="pagination-link"
                                    :class="
                                        link.active
                                            ? 'pagination-link-active'
                                            : 'pagination-link-inactive'
                                    "
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                                <span v-else class="pagination-link-disabled">
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
