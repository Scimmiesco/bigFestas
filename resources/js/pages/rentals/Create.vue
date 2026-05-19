<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue'; // 👈 Importe o ref
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

// O Vue recebe exatamente a prop 'estoqueDisponivel' que o Controller mandou
const props = defineProps<{
    estoqueDisponivel: {
        cadeiras: number;
        mesas: number;
    };
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
    cliente_nome: '',
    cep: '',
    endereco_entrega: '',
    data_entrega: '',
    data_recolha: '',
    qtd_cadeiras: 0,
    qtd_mesas: 0,
    valor: 0,
    observacoes: '',
});

const buscandoCep = ref(false);

const buscarCep = async () => {
    // Tira qualquer traço ou ponto que o usuário tenha digitado
    const cepLimpo = form.cep.replace(/\D/g, '');

    // Verifica se tem 8 números
    if (cepLimpo.length === 8) {
        buscandoCep.value = true;

        try {
            const response = await fetch(
                `https://viacep.com.br/ws/${cepLimpo}/json/`,
            );
            const data = await response.json();

            if (!data.erro) {
                form.endereco_entrega = `${data.logradouro}, XXXX - ${data.bairro}, ${data.localidade}/${data.uf}`;

                // Limpa o erro caso houvesse algum
                form.errors.endereco_entrega = undefined;
            } else {
                form.errors.cep = 'CEP não encontrado.';
            }
        } catch (error) {
            form.errors.cep = 'Erro ao buscar o CEP.';
        } finally {
            buscandoCep.value = false;
        }
    }
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
                            <Label for="cliente_nome"
                                >Nome do Cliente
                                <span class="text-red-500">*</span></Label
                            >
                            <Input
                                id="cliente_nome"
                                v-model="form.cliente_nome"
                                placeholder="Ex: João da Silva"
                                required
                                autofocus
                            />
                            <InputError :message="form.errors.cliente_nome" />
                        </div>

                        <div class="grid gap-2 md:col-span-2">
                            <Label for="cep">CEP</Label>
                            <div class="relative">
                                <Input
                                    id="cep"
                                    v-model="form.cep"
                                    placeholder="00000-000"
                                    maxlength="9"
                                    @blur="buscarCep"
                                    :disabled="buscandoCep"
                                />
                            </div>
                            <span
                                v-if="buscandoCep"
                                class="animate-pulse text-xs text-blue-500"
                                >Buscando...</span
                            >
                            <InputError :message="form.errors.cep" />
                        </div>

                        <div class="grid gap-2 md:col-span-4">
                            <Label for="endereco_entrega"
                                >Endereço Completo
                                <span class="text-red-500">*</span></Label
                            >
                            <Input
                                id="endereco_entrega"
                                v-model="form.endereco_entrega"
                                placeholder="Ex: Rua das Flores, 123 - Centro"
                                required
                            />
                            <InputError
                                :message="form.errors.endereco_entrega"
                            />
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
                        <div class="grid gap-2">
                            <Label
                                for="qtd_mesas"
                                class="text-amber-900 dark:text-amber-200"
                                >Quantidade de Mesas</Label
                            >
                            <Input
                                id="qtd_mesas"
                                type="number"
                                min="0"
                                :max="props.estoqueDisponivel.mesas"
                                v-model="form.qtd_mesas"
                                class="bg-white dark:bg-gray-900"
                            />
                            <p
                                class="text-xs text-amber-700 dark:text-amber-400"
                            >
                                Disponíveis em estoque:
                                <strong>{{
                                    props.estoqueDisponivel.mesas
                                }}</strong>
                            </p>
                            <InputError :message="form.errors.qtd_mesas" />
                        </div>
                        <div class="grid gap-2">
                            <Label
                                for="qtd_cadeiras"
                                class="text-amber-900 dark:text-amber-200"
                                >Quantidade de Cadeiras</Label
                            >
                            <Input
                                id="qtd_cadeiras"
                                type="number"
                                min="0"
                                :max="props.estoqueDisponivel.cadeiras"
                                v-model="form.qtd_cadeiras"
                                class="bg-white dark:bg-gray-900"
                            />
                            <p
                                class="text-xs text-amber-700 dark:text-amber-400"
                            >
                                Disponíveis em estoque:
                                <strong>{{
                                    props.estoqueDisponivel.cadeiras
                                }}</strong>
                            </p>
                            <InputError :message="form.errors.qtd_cadeiras" />
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
