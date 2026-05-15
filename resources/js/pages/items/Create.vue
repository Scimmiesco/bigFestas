<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import itemsRoutes from '@/routes/items/index';
import type { Team } from '@/types';

defineProps<{
    enums?: any;
}>();

const page = usePage();
const teamSlug = page.props.currentTeam?.slug;

// Configuração do Breadcrumb (Trilha de navegação)
defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Estoque',
                href: pageProps.currentTeam
                    ? itemsRoutes.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
            {
                title: 'Novo Item',
                href: '#', // Como já estamos na página, não precisa de link
            },
        ],
    }),
});

// Iniciando o formulário com os campos requeridos pelo StoreItemRequest
const form = useForm({
    nome: '',
    tipo: '',
    status: '',
    observacoes: '',
});

// Função de envio
const submit = () => {
    // Envia o formulário via POST para a rota de salvar itens
    form.post(`/${teamSlug}/itens/`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Adicionar Novo Item" />

    <div class="mx-auto max-w-3xl py-6">
        <Heading
            variant="small"
            title="Adicionar Item ao Estoque"
            description="Preencha os detalhes abaixo para registrar um novo item."
        />

        <div
            class="mt-6 rounded-xl border border-sidebar-border bg-white p-6 shadow-sm dark:bg-sidebar"
        >
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="nome"
                        >Nome do Item <span class="text-red-500">*</span></Label
                    >
                    <Input
                        id="nome"
                        v-model="form.nome"
                        placeholder="Ex: Cadeira Plástica Branca"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.nome" />
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="tipo"
                            >Tipo <span class="text-red-500">*</span></Label
                        >
                        <select
                            id="tipo"
                            v-model="form.tipo"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option value="" disabled selected>
                                Selecione um tipo
                            </option>
                            <option
                                v-for="tipo in enums?.item_types"
                                :key="tipo.value"
                                :value="tipo.value"
                            >
                                {{ tipo.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.tipo" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="status"
                            >Status Inicial
                            <span class="text-red-500">*</span></Label
                        >
                        <select
                            id="status"
                            v-model="form.status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            required
                        >
                            <option value="" disabled selected>
                                Selecione um status
                            </option>
                            <option
                                v-for="status in enums?.item_status"
                                :key="status.value"
                                :value="status.value"
                            >
                                {{ status.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.status" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="observacoes">Observações (Opcional)</Label>
                    <textarea
                        id="observacoes"
                        v-model="form.observacoes"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Detalhes adicionais sobre o estado do item..."
                    ></textarea>
                    <InputError :message="form.errors.observacoes" />
                </div>

                <div
                    class="mt-6 flex items-center justify-end gap-4 border-t pt-6 dark:border-sidebar-border"
                >
                    <Button type="button" variant="outline" as-child>
                        <Link :href="itemsRoutes.index.url(teamSlug as string)"
                            >Cancelar</Link
                        >
                    </Button>

                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Salvando...' : 'Salvar Item' }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
