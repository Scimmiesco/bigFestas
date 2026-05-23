<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Calendar, Truck, CalendarPlus, CheckCheck } from 'lucide-vue-next';
import { ref } from 'vue';
import RentalDetails from '@/components/RentalDetails.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface AgendaItem {
    id: number;
    operacao: 'entrega' | 'recolha';
    cliente: string;
    horario: string;
    endereco: string;
    quantidade: string;
    valor: string;
}

const props = defineProps<{
    agendaSemanal: Record<string, AgendaItem[]>;
    teamSlug: string;
}>();

// --- Lógica do Modal de Detalhes ---
const isModalOpen = ref(false);
const isLoading = ref(false);
const locacaoSelecionada = ref<any>(null);

const abrirModalLocacao = async (id: number) => {
    isModalOpen.value = true;
    isLoading.value = true;
    locacaoSelecionada.value = null;

    try {
        const response = await fetch(`/${props.teamSlug}/locacoes/${id}`, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await response.json();
        locacaoSelecionada.value = data.rental;
    } catch (error) {
        console.error('Erro ao buscar locação', error);
    } finally {
        isLoading.value = false;
    }
};

// --- LÓGICA DO SWIPE (Agora com Chave Única) ---
// Em vez de guardar o ID, guardamos uma string única para identificar exatamente o card
const swipedItemKey = ref<string | null>(null);
let startX = 0;

const onDragStart = (e: TouchEvent | MouseEvent) => {
    startX = 'changedTouches' in e ? e.changedTouches[0].screenX : e.screenX;
};

const onDragEnd = (e: TouchEvent | MouseEvent, uniqueKey: string) => {
    const endX =
        'changedTouches' in e ? e.changedTouches[0].screenX : e.screenX;
    const distance = startX - endX;

    if (distance > 50) {
        swipedItemKey.value = uniqueKey;
    } else if (distance < -50 && swipedItemKey.value === uniqueKey) {
        swipedItemKey.value = null;
    }
};

// --- LÓGICA DE CONFIRMAÇÃO ---
const isConfirmModalOpen = ref(false);
const itemToConfirm = ref<AgendaItem | null>(null);

const abrirConfirmacao = (item: AgendaItem) => {
    itemToConfirm.value = item;
    isConfirmModalOpen.value = true;
};

const confirmarAcaoFinal = async () => {
    if (!itemToConfirm.value) return;

    try {
        // Exemplo de requisição dinâmica baseada na operação:
        // const endpoint = itemToConfirm.value.operacao === 'entrega' ? 'entregar' : 'retirar';
        // await fetch(`/${props.teamSlug}/locacoes/${itemToConfirm.value.id}/${endpoint}`, { method: 'POST' });

        console.log(
            `Sucesso: ${itemToConfirm.value.operacao} concluída para o ID`,
            itemToConfirm.value.id,
        );

        isConfirmModalOpen.value = false;
        swipedItemKey.value = null;
        itemToConfirm.value = null;
    } catch (error) {
        console.error('Erro ao confirmar a ação', error);
    }
};
</script>

<template>
    <div
        class="flex items-center gap-2 rounded border border-b border-solid p-2"
    >
        <Calendar class="h-6 w-6" />
        <h3 class="font-bold tracking-wider uppercase">Agenda da Semana</h3>
        <Button as-child class="ml-auto">
            <Link :href="`/${props.teamSlug}/locacoes/criar`">
                <CalendarPlus />
            </Link>
        </Button>
    </div>
    <div
        class="flex max-h-[360px] min-h-[50vh] flex-1 flex-col rounded-sm border border-solid p-2"
    >
        <div
            class="custom-scrollbar flex-1 space-y-3 overflow-y-auto font-mono text-xs"
        >
            <div
                v-if="Object.keys(agendaSemanal).length === 0"
                class="py-4 text-center"
            >
                Nenhuma entrega ou recolha agendada para os próximos dias.
            </div>

            <div
                v-for="(compromissos, dia) in agendaSemanal"
                :key="dia"
                class="space-y-1"
            >
                <div
                    class="text-md sticky top-0 z-10 rounded bg-primary px-2 py-1 font-bold tracking-wide text-primary-foreground uppercase"
                >
                    {{ dia }}
                </div>

                <div class="space-y-1 pl-1">
                    <div
                        v-for="item in compromissos"
                        :key="`${dia}-${item.id}-${item.operacao}`"
                        class="relative overflow-hidden rounded-sm border border-solid"
                        :class="[
                            item.operacao === 'entrega'
                                ? 'border-chart-3'
                                : 'border-chart-5',
                        ]"
                    >
                        <!-- Camada de Fundo Dinâmica (Verde p/ Entrega, Azul p/ Retirada) -->
                        <div
                            class="absolute inset-y-0 right-0 flex w-24 items-center justify-center transition-colors"
                            :class="
                                item.operacao === 'entrega'
                                    ? 'bg-green-600 hover:bg-chart-2'
                                    : 'bg-blue-600 hover:bg-blue-700'
                            "
                        >
                            <button
                                @click.stop="abrirConfirmacao(item)"
                                class="h-full w-full text-[11px] font-bold text-white uppercase"
                            >
                                {{
                                    item.operacao === 'entrega'
                                        ? 'Entregue'
                                        : 'Retirado'
                                }}
                            </button>
                        </div>

                        <!-- Card Principal: Usa a chave unindo Dia+ID+Operação -->
                        <div
                            @click="abrirModalLocacao(item.id)"
                            @touchstart="onDragStart"
                            @touchend="
                                onDragEnd(
                                    $event,
                                    `${dia}-${item.id}-${item.operacao}`,
                                )
                            "
                            class="relative flex cursor-pointer items-center justify-between gap-2 bg-background p-2 transition-transform duration-300"
                            :class="
                                swipedItemKey ===
                                `${dia}-${item.id}-${item.operacao}`
                                    ? '-translate-x-24'
                                    : 'translate-x-0'
                            "
                        >
                            <!-- Dados do Card -->
                            <div class="flex min-w-0 flex-1 items-center gap-2">
                                <span class="shrink-0 font-bold">{{
                                    item.horario
                                }}</span>
                                <Truck
                                    class="h-5 w-5 shrink-0"
                                    :class="
                                        item.operacao === 'entrega'
                                            ? 'text-chart-3'
                                            : '0 rotate-y-180 text-chart-5'
                                    "
                                />
                                <span
                                    class="truncate font-bold"
                                    :title="item.cliente"
                                >
                                    {{ item.cliente }}
                                </span>
                            </div>

                            <div
                                class="flex shrink-0 flex-col items-center gap-1 font-mono text-[12px]"
                            >
                                <div
                                    class="rounded-sm border bg-primary px-1 py-0.5 text-primary-foreground"
                                >
                                    <span>{{
                                        new Intl.NumberFormat('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL',
                                        }).format(parseInt(item.valor ?? '0'))
                                    }}</span>
                                </div>
                                <div
                                    class="rounded-sm border px-1 py-0.5 font-bold"
                                >
                                    {{ item.quantidade }}
                                </div>
                            </div>
                            <div
                                @click.stop="abrirConfirmacao(item)"
                                class="flex-no-wrap align-center hidden w-min grow-0 gap-1 text-center md:flex"
                            >
                                <span
                                    class="overflow-w align-center relative text-center text-wrap wrap-break-word"
                                    :class="
                                        item.operacao === 'entrega'
                                            ? 'text-chart-3'
                                            : 'text-chart-5'
                                    "
                                >
                                    {{
                                        item.operacao === 'entrega'
                                            ? 'Confirmar entrega'
                                            : 'Confirmar retirada'
                                    }}
                                    <CheckCheck
                                        class="absolute top-1/2 left-1/2 h-8 w-8 -translate-x-1/2 -translate-y-1/2"
                                    />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalhes -->
    <Dialog v-model:open="isModalOpen">
        <DialogContent class="max-h-[90vh] border sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle class="tracking-wider"
                    >Detalhes do Agendamento</DialogTitle
                >
            </DialogHeader>
            <div v-if="isLoading" class="py-12 text-center font-mono">
                Carregando informações...
            </div>
            <div v-else-if="locacaoSelecionada">
                <RentalDetails :rental="locacaoSelecionada" />
            </div>
        </DialogContent>
    </Dialog>

    <!-- Modal de Confirmação Dinâmico -->
    <Dialog v-model:open="isConfirmModalOpen">
        <DialogContent class="sm:max-w-[400px]">
            <DialogHeader>
                <DialogTitle class="text-xl font-bold tracking-wider">
                    Confirmar
                    {{
                        itemToConfirm?.operacao === 'entrega'
                            ? 'Entrega'
                            : 'Retirada'
                    }}
                </DialogTitle>
            </DialogHeader>

            <div class="py-4">
                <p>
                    Tem certeza que deseja marcar a
                    <strong>{{
                        itemToConfirm?.operacao === 'entrega'
                            ? 'entrega'
                            : 'retirada'
                    }}</strong>
                    de <strong>{{ itemToConfirm?.cliente }}</strong> como
                    concluída?
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button
                    @click="isConfirmModalOpen = false"
                    class="rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                >
                    Cancelar
                </button>
                <button
                    @click="confirmarAcaoFinal"
                    class="rounded-md px-4 py-2 text-sm font-medium text-white transition-colors"
                    :class="
                        itemToConfirm?.operacao === 'entrega'
                            ? 'bg-green-600 hover:bg-green-700'
                            : 'bg-blue-600 hover:bg-blue-700'
                    "
                >
                    Sim, Confirmar
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    border-radius: 2px;
}
</style>
