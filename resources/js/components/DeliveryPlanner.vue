<script setup lang="ts">
import { Calendar, Truck } from 'lucide-vue-next';

import { ref } from 'vue';
import RentalDetails from '@/components/RentalDetails.vue';
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

// 👇 Atribuindo o defineProps a uma variável para podermos usar o teamSlug no Axios
const props = defineProps<{
    agendaSemanal: Record<string, AgendaItem[]>;
    teamSlug: string;
}>();

// --- Lógica do Modal ---
const isModalOpen = ref(false);
const isLoading = ref(false);
const locacaoSelecionada = ref<any>(null);
// Pode remover a linha: import axios from 'axios';

const abrirModalLocacao = async (id: number) => {
    isModalOpen.value = true;
    isLoading.value = true;
    locacaoSelecionada.value = null;

    try {
        // 1. Usamos o fetch nativo passando a URL
        const response = await fetch(`/${props.teamSlug}/locacoes/${id}`, {
            headers: {
                Accept: 'application/json', // 👈 Isso avisa o Laravel para devolver JSON (nosso truque!)
                'X-Requested-With': 'XMLHttpRequest', // 👈 Garante que o Laravel saiba que é uma requisição assíncrona
            },
        });

        // 2. Com fetch, precisamos converter a resposta para JSON manualmente
        const data = await response.json();

        // 3. Pegamos os dados normalmente
        locacaoSelecionada.value = data.rental;
    } catch (error) {
        console.error('Erro ao buscar locação', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div
        class="flex max-h-[360px] min-h-[50vh] flex-1 flex-col rounded-sm border-2 border-solid border-amber-100 p-3"
    >
        <!-- Topo fixo da Agenda -->
        <div
            class="mb-2 flex items-center gap-2 border-b border-solid border-amber-100 pb-2 text-amber-100"
        >
            <Calendar class="h-5 w-5" />
            <h3 class="text-xs font-bold tracking-wider uppercase">
                Agenda da Semana
            </h3>
        </div>

        <!-- Área de Scroll da lista (Estilo Google Agenda) -->
        <div
            class="custom-scrollbar flex-1 space-y-3 overflow-y-auto pr-1 font-mono text-xs"
        >
            <div
                v-if="Object.keys(agendaSemanal).length === 0"
                class="py-4 text-center text-amber-100"
            >
                Nenhuma entrega ou recolha agendada para os próximos dias.
            </div>

            <div
                v-for="(compromissos, dia) in agendaSemanal"
                :key="dia"
                class="space-y-1"
            >
                <!-- Label do Dia (Hoje, Amanhã, Segunda-feira...) -->
                <div
                    class="text-md sticky top-0 z-10 rounded-sm bg-amber-100 px-1 font-bold tracking-wide text-black uppercase"
                >
                    {{ dia }}
                </div>

                <!-- Compromissos do Dia -->
                <div class="space-y-1 pl-1">
                    <!-- 👇 Troquei o <Link> por uma div com @click e cursor-pointer -->
                    <div
                        v-for="item in compromissos"
                        :key="`${item.id}-${item.operacao}`"
                        @click="abrirModalLocacao(item.id)"
                        class="flex cursor-pointer items-center justify-between gap-2 rounded-sm border border-solid p-2 transition-colors hover:bg-neutral-800/30"
                        :class="[
                            item.operacao === 'entrega'
                                ? 'border-indigo-500'
                                : 'border-amber-500',
                        ]"
                    >
                        <!-- Esquerda (Hora, Ícone e Cliente) -->
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <span class="shrink-0 font-bold text-amber-100">{{
                                item.horario
                            }}</span>
                            <Truck
                                class="h-5 w-5 shrink-0"
                                :class="
                                    item.operacao === 'entrega'
                                        ? 'text-indigo-500'
                                        : 'rotate-y-180 text-amber-500'
                                "
                            />
                            <span
                                class="truncate font-bold"
                                :title="item.cliente"
                            >
                                {{ item.cliente }}
                            </span>
                        </div>

                        <!-- Direita (Tags de Carga e Valor lado a lado) -->
                        <div
                            class="flex shrink-0 items-center gap-1 font-mono text-[12px]"
                        >
                            <!-- Tag do Valor -->
                            <div
                                class="rounded-sm border bg-amber-100 px-1 py-0.5 text-black"
                            >
                                <span>{{
                                    new Intl.NumberFormat('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL',
                                    }).format(parseInt(item.valor ?? 0))
                                }}</span>
                            </div>
                            <!-- Tag da Carga -->
                            <div
                                class="rounded-sm border border-amber-100 px-1 py-0.5 font-bold text-amber-50"
                            >
                                {{ item.quantidade }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 👇 O Modal (Dialog) que será aberto -->
    <Dialog v-model:open="isModalOpen">
        <DialogContent
            class="border-amber-100/20 bg-neutral-900 text-amber-50 sm:max-w-[600px]"
        >
            <DialogHeader>
                <DialogTitle class="tracking-wider text-amber-100"
                    >Detalhes do Agendamento</DialogTitle
                >
            </DialogHeader>

            <!-- Loading enquanto o axios busca no banco -->
            <div
                v-if="isLoading"
                class="py-12 text-center font-mono text-amber-500/70"
            >
                Carregando informações...
            </div>

            <!-- O componente puro preenchido com os dados -->
            <div v-else-if="locacaoSelecionada">
                <RentalDetails :rental="locacaoSelecionada" />
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Scrollbar customizada e fina para não quebrar o layout */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #fcd34d; /* amber-300 */
    border-radius: 2px;
}
</style>
