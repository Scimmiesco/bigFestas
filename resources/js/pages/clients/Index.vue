<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import clients from '@/routes/clients/index';
import type { Team } from '@/types';

interface Client {
    id: number;
    nome: string;
    natureza_juridica: string;
    cpf_cnpj: string;
}

interface PaginatedData {
    data?: Client[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    clientes: PaginatedData;
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Clientes',
                href: pageProps.currentTeam?.slug
                    ? clients.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
        ],
    }),
});

const page = usePage(); 
const teamSlug = computed(() => page.props.currentTeam?.slug);

// --- MÉTODO DE EXCLUSÃO INDIVIDUAL ---
const deletar = (id: number) => {
    if (
        confirm('Tem certeza que deseja excluir este cliente permanentemente?')
    ) {
        router.delete(`/${teamSlug.value}/clientes/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Clientes" />

    <div class="page-container">
        <div class="page-header">
            <Heading
                variant="default"
                title="Gerenciar Clientes"
                description="Visualize todos os clientes cadastrados no seu time."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/clientes/criar`">
                    <Plus class="h-4 w-4" />
                    Adicionar Novo Cliente
                </Link>
            </Button>
        </div>

        <div class="table-wrapper">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="table-head">
                        <tr>
                            <th class="th-cell text-nowrap">Nome do Cliente</th>
                            <th class="th-cell text-center">
                                Natureza Jurídica
                            </th>
                            <th class="th-cell text-center">CPF / CNPJ</th>
                            <th class="th-cell text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-if="props?.clientes?.data?.length === 0">
                            <td
                                colspan="4"
                                class="empty-state py-4 text-center"
                            >
                                Nenhum cliente cadastrado ainda.
                            </td>
                        </tr>

                        <tr
                            v-for="cliente in props?.clientes?.data"
                            :key="cliente.id"
                            class="group table-row"
                        >
                            <td class="td-cell font-medium">
                                <div class="item-name">
                                    {{ cliente.nome }}
                                </div>
                            </td>

                            <td class="td-cell text-center font-mono uppercase">
                                {{ cliente.natureza_juridica }}
                            </td>
                            <td
                                class="td-cell text-center font-mono text-sm tracking-wider uppercase"
                            >
                                {{ cliente.cpf_cnpj }}
                            </td>
                            <td class="td-cell">
                                <div
                                    class="action-buttons flex justify-end gap-3"
                                >
                                    <Link
                                        :href="`/${teamSlug}/clientes/${cliente.id}/editar`"
                                        class="btn-edit flex items-center gap-1"
                                    >
                                        <Pencil class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Editar</span
                                        >
                                    </Link>

                                    <button
                                        @click="deletar(cliente.id)"
                                        type="button"
                                        class="btn-delete flex items-center gap-1"
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

            <!-- <div
                class="pagination-wrapper"
                v-if="props.clientes?.links?.length > 3"
            >
                <div class="pagination-mobile">
                    <Link
                        :href="props.clientes.links[0].url ?? '#'"
                        class="btn-pagination"
                    >
                        Anterior
                    </Link>
                    <Link
                        :href="
                            props.clientes.links[
                                props.clientes.links.length - 1
                            ].url ?? '#'
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
                                props.clientes.current_page
                            }}</span>
                            de
                            <span class="font-medium">{{
                                props.clientes.last_page
                            }}</span>
                        </p>
                    </div>
                    <div>
                        <nav class="pagination-nav" aria-label="Pagination">
                            <template
                                v-for="(link, index) in props.clientes.links"
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
            </div> -->
        </div>
    </div>
</template>
