<script setup lang="ts">
import {
    Armchair,
    Layout,
    Package,
    Truck,
    CheckCircle2,
    Wrench,
    Snowflake,
} from 'lucide-vue-next';

// 1. Tipamos o que este componente espera receber
interface EstoqueDetalhe {
    tipo: string; // Ex: 'cadeira', 'mesa' (usado para descobrir o ícone)
    nome: string; // Ex: 'Cadeiras', 'Mesas'
    disponivel: number;
    alugado: number;
    manutencao: number;
    total: number;
}

// 👇 CORREÇÃO AQUI: Faltava fechar o }>();
defineProps<{
    item: EstoqueDetalhe;
}>();

// 2. Lógica da porcentagem movida para cá (fica isolada e limpa)
const getPercentage = (valor: number, total: number) => {
    if (total === 0) {
        return 0;
    }

    return Math.round((valor / total) * 100);
};

const iconesMap: Record<string, any> = {
    cadeira: Armchair, // Bate com ItemType::Cadeira ('cadeira')
    mesa: Layout, // Bate com ItemType::Mesa ('mesa')
    cooler: Snowflake, // Bate com ItemType::Cooler ('cooler')
    default: Package, // Se você cadastrar "Tenda" no banco e esquecer de atualizar o Vue, ele usa a caixa!
};

const getIcone = (tipo: string) => {
    // Garante que está minúsculo para evitar bugs de case-sensitive
    return iconesMap[tipo.toLowerCase()] || iconesMap.default;
};
</script>

<template>
    <div
        class="flex-1 rounded-sm border border-solid border-amber-100 px-3 py-2"
    >
        <div class="flex items-center justify-between p-1 pb-3">
            <div class="flex items-center gap-2">
                <component
                    :is="getIcone(item.tipo)"
                    class="h-6 w-6 text-amber-600"
                />
                <h3 class="font-bold tracking-wider uppercase">
                    {{ item.nome }}
                </h3>
            </div>
            <span class="text-sm font-bold tracking-wider">{{
                item.total
            }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div>
                <div
                    class="flex justify-between pb-2 font-mono font-bold text-emerald-500"
                >
                    <span class="flex items-center gap-1">
                        <CheckCircle2 class="h-5 w-5" /> Disponíveis
                    </span>
                    <span>{{ item.disponivel }}</span>
                </div>
                <div class="h-2 w-full rounded-full bg-amber-100">
                    <div
                        class="h-2 rounded-full bg-emerald-500 transition-all duration-500"
                        :style="{
                            width: `${getPercentage(item.disponivel, item.total)}%`,
                        }"
                    ></div>
                </div>
            </div>

            <div>
                <div
                    class="flex justify-between pb-2 font-mono font-bold text-blue-500"
                >
                    <span class="flex items-center gap-1">
                        <Truck class="h-5 w-5" /> Alugadas
                    </span>
                    <span>{{ item.alugado }}</span>
                </div>
                <div class="h-2 w-full rounded-full bg-amber-100">
                    <div
                        class="h-2 rounded-full bg-blue-500 transition-all duration-500"
                        :style="{
                            width: `${getPercentage(item.alugado, item.total)}%`,
                        }"
                    ></div>
                </div>
            </div>

            <div>
                <div
                    class="flex justify-between pb-2 font-mono font-bold text-rose-500"
                >
                    <span class="flex items-center gap-1">
                        <Wrench class="h-5 w-5" /> Manutenção
                    </span>
                    <span>{{ item.manutencao }}</span>
                </div>
                <div class="h-2 w-full rounded-full bg-amber-100">
                    <div
                        class="h-2 rounded-full bg-rose-500 transition-all duration-500"
                        :style="{
                            width: `${getPercentage(item.manutencao, item.total)}%`,
                        }"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>
