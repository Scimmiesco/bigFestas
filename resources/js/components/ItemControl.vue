<script setup lang="ts">
import { computed } from 'vue';

import type { Item } from '@/types/items';

const props = defineProps<{
    title: string;
    items: Item[];
}>();

// Cores mapeadas para o status (facilitando o "olho clínico" no mobile)
const statusColors = {
    disponivel: 'bg-green-500',
    alugado: 'bg-blue-500',
    avariado: 'bg-red-500',
    manutencao: 'bg-orange-500',
};

const total = computed(() => props.items.length);
const disponiveis = computed(
    () => props.items.filter((i) => i.status === 'disponivel').length,
);
</script>

<template>
    <div class="rounded-xl border bg-white p-4 shadow-sm dark:bg-zinc-900">
        <div class="mb-3 flex items-end justify-between">
            <div>
                <h3 class="text-lg font-bold">{{ title }}</h3>
                <p class="text-xs text-zinc-500">
                    {{ disponiveis }} de {{ total }} disponíveis
                </p>
            </div>
            <button
                class="text-xs font-bold tracking-wider text-blue-500 uppercase"
            >
                Ver tudo
            </button>
        </div>

        <div class="flex flex-wrap gap-1.5">
            <div
                v-for="item in items"
                :key="item.id"
                :class="[
                    statusColors[item.status],
                    'h-3 w-3 rounded-[2px] opacity-80',
                ]"
                :title="item.status"
            ></div>
        </div>
    </div>
</template>
