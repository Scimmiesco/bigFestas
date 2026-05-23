<script setup lang="ts">
import { Boxes } from 'lucide-vue-next';
import ItemStatus from '@/components/ItemStatus.vue';

interface EstoqueDetalhe {
    tipo: string;
    nome: string;
    disponivel: number;
    alugado: number;
    manutencao: number;
    total: number;
}

interface StockData {
    jogos_disponiveis: number;
    cadeiras_avulsas: number;
    estoque: EstoqueDetalhe[];
    agenda_semanal: any; // 👈 Atualizado aqui
}

defineProps<{
    data: StockData;
}>();
</script>

<template>
    <div class="flex flex-col gap-2">
        <div
            class="flex items-center gap-2 rounded border border-b border-solid p-2"
        >
            <Boxes class="h-6 w-6" />
            <h3 class="font-bold tracking-wider uppercase">
                Resumo do estoque
            </h3>
        </div>
        <div class="flex flex-col gap-4 md:flex-row">
            <!-- Bloco: Prontos para Alugar -->
            <div class="flex-1 rounded-sm border px-4 py-3">
                <div class="flex h-full flex-col justify-between">
                    <p class="text-xs font-bold tracking-wider uppercase">
                        Prontos para Alugar
                    </p>
                    <div class="mt-4 mb-2 flex items-baseline gap-2">
                        <h2 class="text-5xl font-black">
                            {{ data.jogos_disponiveis }}
                        </h2>
                        <span
                            class="text-sm font-bold tracking-wider uppercase"
                        >
                            Jogos
                        </span>
                        <p v-if="data.cadeiras_avulsas != 0" class="">
                            e
                            <b class="text-2xl">{{ data.cadeiras_avulsas }} </b>
                            avulsas
                        </p>
                    </div>
                    <p class="font-mono text-xs font-medium">
                        (1 mesa / 4 cadeiras disponíveis)
                    </p>
                </div>
            </div>
        </div>

        <div
            class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
        >
            <ItemStatus
                v-for="item in data.estoque"
                :key="item.tipo"
                :item="item"
            />
        </div>
    </div>
</template>
