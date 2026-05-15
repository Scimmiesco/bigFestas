<script setup lang="ts">
interface InventoryDetail {
    disponiveis: number;
    total: number;
}

defineProps<{
    jogos_prontos: number;
    cadeiras: InventoryDetail;
    mesas: InventoryDetail;
}>();
</script>

<template>
    <div
        class="rounded-md border border-cyan-50 bg-cyan-900 p-5 text-white shadow-lg shadow-amber-200 dark:shadow-none"
    >
        <div class="flex flex-col gap-1">
            <span class="text-xs font-bold tracking-wider uppercase opacity-90">
                Disponibilidade para Aluguel
            </span>

            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-6xl font-black">{{ jogos_prontos }}</span>
                <span class="text-xl font-bold">Jogos Prontos</span>
            </div>

            <p class="mt-1 text-sm font-medium opacity-90">
                Pronto para entrega imediata
            </p>

            <hr class="my-4 border-white/20" />

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase opacity-80"
                        >Cadeiras Livres</span
                    >
                    <span class="text-lg font-bold"
                        >{{ cadeiras?.disponiveis }}
                        <span class="text-xs opacity-70"
                            >/ {{ cadeiras?.total }}</span
                        ></span
                    >
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase opacity-80"
                        >Mesas Livres</span
                    >
                    <span class="text-lg font-bold"
                        >{{ mesas?.disponiveis }}
                        <span class="text-xs opacity-70"
                            >/ {{ mesas?.total }}</span
                        ></span
                    >
                </div>
            </div>

            <p class="mt-4 text-[10px] leading-tight italic opacity-70">
                * Regra: 1 mesa + 4 cadeiras por jogo.
            </p>
        </div>
    </div>
</template>
