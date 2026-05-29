<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, MapPin } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import type { Team } from '@/types';
// Assumindo que você tenha suas rotas configuradas para endereços
import addresses from '@/routes/addresses/index';

interface Client {
    id: number;
    nome: string;
}

interface Address {
    id: number;
    tipo_endereco: string;
    logradouro: string;
    numero: string | null;
    bairro: string;
    cidade: string;
    uf: string;
    // O relacionamento com o cliente carregado no Controller
    client?: Client;
}

interface PaginatedData {
    data: Address[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    enderecos: PaginatedData;
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Endereços',
                href: pageProps.currentTeam?.slug
                    ? address.index.url(pageProps.currentTeam.slug)
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
        confirm('Tem certeza que deseja excluir este endereço permanentemente?')
    ) {
        router.delete(`/${teamSlug.value}/enderecos/${id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Endereços" />

    <div class="page-container">
        <div class="page-header">
            <Heading
                variant="default"
                title="Gerenciar Endereços"
                description="Visualize todos os endereços vinculados aos seus clientes."
            />

            <Button as-child>
                <Link :href="`/${teamSlug ?? ''}/enderecos/criar`">
                    <Plus class="mr-2 h-4 w-4" />
                    Adicionar Novo Endereço
                </Link>
            </Button>
        </div>

        <div class="table-wrapper">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="table-head">
                        <tr>
                            <th class="th-cell text-nowrap">Cliente</th>
                            <th class="th-cell text-center">Tipo</th>
                            <th class="th-cell">Endereço Completo</th>
                            <th class="th-cell text-center">Cidade / UF</th>
                            <th class="th-cell text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <tr v-if="props.enderecos.data.length === 0">
                            <td
                                colspan="5"
                                class="empty-state py-6 text-center"
                            >
                                <div
                                    class="flex flex-col items-center justify-center text-muted-foreground"
                                >
                                    <MapPin class="mb-2 h-8 w-8 opacity-50" />
                                    <p>Nenhum endereço cadastrado ainda.</p>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-for="endereco in props.enderecos.data"
                            :key="endereco.id"
                            class="group table-row"
                        >
                            <td class="td-cell font-medium">
                                <div class="item-name text-primary">
                                    {{
                                        endereco.client?.nome ||
                                        'Cliente não encontrado'
                                    }}
                                </div>
                            </td>

                            <td
                                class="td-cell text-center font-mono text-xs uppercase"
                            >
                                <span
                                    class="rounded-md bg-gray-100 px-2 py-1 text-gray-700"
                                >
                                    {{ endereco.tipo_endereco }}
                                </span>
                            </td>

                            <td class="td-cell text-sm">
                                <div
                                    class="max-w-[250px] truncate"
                                    :title="`${endereco.logradouro}, ${endereco.numero ?? 'S/N'} - ${endereco.bairro}`"
                                >
                                    {{ endereco.logradouro }},
                                    {{ endereco.numero ?? 'S/N' }}
                                    <span class="block text-xs text-gray-500">{{
                                        endereco.bairro
                                    }}</span>
                                </div>
                            </td>

                            <td class="td-cell text-center text-sm">
                                {{ endereco.cidade }} -
                                <span class="font-bold">{{ endereco.uf }}</span>
                            </td>

                            <td class="td-cell">
                                <div
                                    class="action-buttons flex justify-end gap-3"
                                >
                                    <Link
                                        :href="`/${teamSlug}/enderecos/${endereco.id}/editar`"
                                        class="btn-edit flex items-center gap-1"
                                    >
                                        <Pencil class="h-4 w-4" />
                                        <span class="hidden sm:inline"
                                            >Editar</span
                                        >
                                    </Link>

                                    <button
                                        @click="deletar(endereco.id)"
                                        type="button"
                                        class="btn-delete flex items-center gap-1 text-red-600 transition-colors hover:text-red-800"
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
                v-if="props.enderecos.links.length > 3"
            >
                <div class="pagination-mobile">
                    <Link
                        :href="props.enderecos.links[0].url ?? '#'"
                        class="btn-pagination"
                    >
                        Anterior
                    </Link>
                    <Link
                        :href="
                            props.enderecos.links[
                                props.enderecos.links.length - 1
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
                                props.enderecos.current_page
                            }}</span>
                            de
                            <span class="font-medium">{{
                                props.enderecos.last_page
                            }}</span>
                        </p>
                    </div>
                    <div>
                        <nav class="pagination-nav" aria-label="Pagination">
                            <template
                                v-for="(link, index) in props.enderecos.links"
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
