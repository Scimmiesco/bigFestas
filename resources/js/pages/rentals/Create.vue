<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue'; // 👈 Importe o ref
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

const props = defineProps<{
    estoqueDisponivel: Array<{
        type: string;
        label: string;
        available: number;
    }>;
    clients: Array<{
        id: number;
        nome: string;
        addresses: Array<{
            id: number;
            tipo_endereco: string;
            logradouro: string;
            numero: string;
            bairro: string;
            cidade: string;
            uf: string;
        }>;
    }>;
}>();

const page = usePage();
const teamSlug = computed(() => page.props.currentTeam?.slug);

// Trilha de navegação (Breadcrumbs)
defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Locações',
                href: pageProps.currentTeam?.slug
                    ? `/${pageProps.currentTeam.slug}/locacoes`
                    : '/',
            },
            {
                title: 'Nova Locação',
                href: '#',
            },
        ],
    }),
});

// Iniciando o formulário com TODOS os campos, incluindo as observações
const form = useForm({
    client_id: '',
    address_id: '',
    data_entrega: '',
    data_recolha: '',
    items: {} as Record<string, number>,
    valor: 0,
    observacoes: '',
});

// Inicializa a contagem de todos os itens com 0
props.estoqueDisponivel.forEach(item => {
    form.items[item.type] = 0;
});

// Computed para filtrar os endereços com base no cliente selecionado
const availableAddresses = computed(() => {
    const client = props.clients.find(c => c.id === form.client_id);
    return client ? client.addresses : [];
});

// Reseta o endereço se o cliente mudar
const onClientChange = () => {
    form.address_id = '';
};



// Envia os dados para o método store do RentalController
const submit = () => {
    form.post(`/${teamSlug.value}/locacoes`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nova Locação" />

    <div class="mx-auto max-w-4xl p-4 py-6">
        <Heading
            variant="small"
            title="Agendar Nova Locação"
            description="Preencha os detalhes do cliente e selecione a quantidade de itens."
        />

        <div
            class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
        >
            <form @submit.prevent="submit" class="space-y-8">
                <div class="space-y-4">
                    <h3
                        class="border-b pb-2 text-lg font-medium text-gray-900 dark:border-gray-700 dark:text-gray-100"
                    >
                        Dados do Cliente e Entrega
                    </h3>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-12">
                        <div class="grid gap-2 md:col-span-6">
                            <Label for="client_id"
                                >Cliente
                                <span class="text-red-500">*</span></Label
                            >
                            <select
                                id="client_id"
                                v-model="form.client_id"
                                @change="onClientChange"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="" disabled selected>Selecione um cliente</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.nome }}
                                </option>
                            </select>
                            <InputError :message="form.errors.client_id" />
                        </div>

                        <div class="grid gap-2 md:col-span-6">
                            <Label for="address_id"
                                >Endereço de Entrega
                                <span class="text-red-500">*</span></Label
                            >
                            <select
                                id="address_id"
                                v-model="form.address_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="!form.client_id"
                                required
                            >
                                <option value="" disabled selected>
                                    {{ form.client_id ? 'Selecione um endereço' : 'Selecione um cliente primeiro' }}
                                </option>
                                <option v-for="address in availableAddresses" :key="address.id" :value="address.id">
                                    {{ address.logradouro }}, {{ address.numero }} - {{ address.bairro }}, {{ address.cidade }}/{{ address.uf }}
                                </option>
                            </select>
                            <InputError :message="form.errors.address_id" />
                            <p v-if="form.client_id && availableAddresses.length === 0" class="text-xs text-red-500 mt-1">
                                Este cliente não possui endereços cadastrados. <Link :href="`/${teamSlug}/enderecos/criar`" class="underline">Cadastrar endereço</Link>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3
                        class="border-b pb-2 text-lg font-medium text-gray-900 dark:border-gray-700 dark:text-gray-100"
                    >
                        Período da Locação
                    </h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="data_entrega"
                                >Data e Hora da Entrega
                                <span class="text-red-500">*</span></Label
                            >
                            <Input
                                id="data_entrega"
                                type="datetime-local"
                                v-model="form.data_entrega"
                                required
                            />
                            <InputError :message="form.errors.data_entrega" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="data_recolha"
                                >Data e Hora da Recolha
                                <span class="text-red-500">*</span></Label
                            >
                            <Input
                                id="data_recolha"
                                type="datetime-local"
                                v-model="form.data_recolha"
                                required
                            />
                            <InputError :message="form.errors.data_recolha" />
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-lg border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-900/10"
                >
                    <h3 class="dark:0 mb-4 text-lg font-medium text-amber-900">
                        Itens para Alugar
                    </h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div v-for="item in props.estoqueDisponivel" :key="item.type" class="grid gap-2">
                            <Label
                                :for="'qtd_' + item.type"
                                class="text-amber-900 dark:text-amber-200"
                                >Quantidade de {{ item.label }}</Label
                            >
                            <Input
                                :id="'qtd_' + item.type"
                                type="number"
                                min="0"
                                :max="item.available"
                                v-model="form.items[item.type]"
                                class="bg-white dark:bg-gray-900"
                            />
                            <p
                                class="text-xs text-amber-700 dark:text-amber-400"
                            >
                                Disponíveis em estoque:
                                <strong>{{ item.available }}</strong>
                            </p>
                            <InputError :message="form.errors['items.' + item.type]" />
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="observacoes">Valor</Label>
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
                        />
                    </div>

                    <InputError :message="form.errors.valor" />
                </div>

                <div class="grid gap-2">
                    <Label for="observacoes"
                        >Observações Adicionais (Opcional)</Label
                    >
                    <textarea
                        id="observacoes"
                        v-model="form.observacoes"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Ex: Entregar pelos fundos, ligar antes de chegar..."
                    ></textarea>
                    <InputError :message="form.errors.observacoes" />
                </div>

                <div
                    class="mt-6 flex items-center justify-end gap-4 border-t pt-6 dark:border-gray-700"
                >
                    <Button type="button" variant="outline" as-child>
                        <Link :href="`/${teamSlug ?? ''}/locacoes`"
                            >Cancelar</Link
                        >
                    </Button>

                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing
                                ? 'Agendando...'
                                : 'Confirmar Locação'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
