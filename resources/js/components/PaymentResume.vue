<script setup lang="ts">
import { DollarSign, Clock, AlertCircle } from 'lucide-vue-next';

defineProps<{
    data: any;
}>();

// Função nativa do Javascript para formatar como moeda brasileira (R$)
const formatarMoeda = (valor: number) => {
    if (!valor) {
        return 'R$ 0,00';
    }

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(valor);
};
</script>

<template>
    <div class="grid min-h-[25vh] grid-cols-2 gap-2 md:grid-cols-3">
        <!-- Card 1: Recebido -->
        <div
            class="flex flex-1 flex-col items-center justify-center rounded-sm border px-2 py-1"
        >
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <DollarSign class="h-6 w-6 shrink-0 text-chart-2" />
                    <h3 class="truncate font-bold tracking-wider uppercase">
                        Recebido
                    </h3>
                </div>
            </div>

            <div class="px-1 pb-1">
                <p
                    class="truncate font-mono text-2xl font-black text-chart-2 sm:text-3xl"
                    :title="formatarMoeda(data.faturamento_total)"
                >
                    {{ formatarMoeda(data.faturamento_total) }}
                </p>
            </div>
        </div>

        <!-- Card 2: A Receber -->
        <div
            class="flex flex-1 flex-col items-center justify-center rounded-sm border border-solid px-2 py-1"
        >
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <Clock class="h-6 w-6 shrink-0 text-chart-3" />
                    <h3 class="truncate font-bold tracking-wider uppercase">
                        A Receber
                    </h3>
                </div>
            </div>

            <div class="px-1 pb-1">
                <p
                    class="truncate font-mono text-2xl font-black text-chart-3 sm:text-3xl"
                    :title="formatarMoeda(data.a_receber)"
                >
                    {{ formatarMoeda(data.a_receber) }}
                </p>
            </div>
        </div>

        <!-- Card 3: Em Atraso -->
        <div
            class="col-span-2 flex flex-1 flex-col items-center justify-center rounded-sm border border-solid px-2 py-1 md:col-span-1"
        >
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <AlertCircle class="h-6 w-6 shrink-0 text-chart-1" />
                    <h3 class="truncate font-bold tracking-wider uppercase">
                        Em Atraso
                    </h3>
                </div>
            </div>

            <div class="px-1 pb-1">
                <p
                    class="truncate font-mono text-2xl font-black text-chart-1"
                    :title="formatarMoeda(data.em_atraso)"
                >
                    {{ formatarMoeda(data.em_atraso) }}
                </p>
            </div>
        </div>
    </div>
</template>
