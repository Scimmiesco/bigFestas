<script setup lang="ts">
import {
    Layout,
    Armchair,
    Snowflake,
    MapPin,
    Package,
    ExternalLink,
} from 'lucide-vue-next'; // Adicionado ExternalLink
import { computed } from 'vue';

const props = defineProps<{
    rental: any; // Tipar de acordo com o seu banco
}>();

// Mapeamento de ícones para itens individuais
const iconesMap: Record<string, any> = {
    cadeira: Armchair,
    mesa: Layout,
    cooler: Snowflake,
    default: Package,
};

// Lógica para gerar o link de busca do Google Maps baseado no endereço de texto
const googleMapsUrl = computed(() => {
    if (!props.rental?.endereco_entrega) return '';
    // Codifica o endereço de texto para ser usado de forma segura em uma URL
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(props.rental.endereco_entrega)}`;
});

// Lógica para gerar a URL do iframe do Google Maps
const googleMapsIframeUrl = computed(() => {
    if (!props.rental?.endereco_entrega) return '';
    // Utiliza o modo "place" do Embed API que é gratuito para buscas por texto simples
    return `https://maps.google.com/maps?q=${encodeURIComponent(props.rental.endereco_entrega)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
});

// Lógica para agrupar e calcular conjuntos e itens extra
const rentalItemGroups = computed(() => {
    if (!props.rental?.items) {
        return {
            sets: 0,
            extraTables: 0,
            extraChairs: 0,
            coolers: 0,
            other: [],
        };
    }

    let tables = 0;
    let chairs = 0;
    let coolers = 0;
    const other: any[] = [];

    // 1. Contar os itens por tipo
    props.rental.items.forEach((item: any) => {
        const tipo = item.tipo?.toLowerCase();
        const quantidade = item.pivot?.quantidade || item.quantidade || 1;

        if (tipo === 'mesa') {
            tables += quantidade;
        } else if (tipo === 'cadeira') {
            chairs += quantidade;
        } else if (tipo === 'cooler') {
            coolers += quantidade;
        } else {
            // Outros itens
            const icone = iconesMap[tipo] || iconesMap.default;
            other.push({ ...item, icone, quantidade });
        }
    });

    // 2. Calcular conjuntos completos (1 mesa + 4 cadeiras)
    const sets = Math.min(tables, Math.floor(chairs / 4));

    // 3. Calcular itens extra
    const extraTables = tables - sets;
    const extraChairs = chairs - sets * 4;

    return { sets, extraTables, extraChairs, coolers, other };
});
</script>

<template>
    <div class="space-y-4">
<<<<<<< HEAD
        <div
            class="flex items-center justify-between gap-2 border border-b pb-2"
        >
=======
        <div class="flex items-center justify-between gap-2 pb-2">
>>>>>>> 8c7c2e0 (ajustes cores)
            <div>
                <h2 class="text-xl font-bold">
                    {{ rental.cliente_nome }}
                </h2>
                <div class="mt-1 flex items-center justify-between">
<<<<<<< HEAD
                    <p class="/70 font-mono text-sm">
                        Locação #{{ rental.id }}
                    </p>
=======
                    <p class="font-mono text-sm">Locação #{{ rental.id }}</p>
>>>>>>> 8c7c2e0 (ajustes cores)
                </div>
            </div>
            <div class="flex flex-col-reverse gap-1">
                <div
                    class="rounded border px-2 py-1 text-xs font-bold uppercase"
                >
                    {{ rental.status }}
                </div>
                <div
                    class="rounded border bg-amber-100 px-2 py-1 text-center text-xs font-bold uppercase"
                >
                    R$ {{ rental.valor }}
                </div>
            </div>
        </div>

        <div
            class="grid grid-cols-1 gap-4 rounded border p-2 text-sm md:grid-cols-2"
        >
            <div class="space-y-1">
                <span class="font-bold">Entrega:</span>
<<<<<<< HEAD
                <p class="/90 font-mono">
=======
                <p class="font-mono">
>>>>>>> 8c7c2e0 (ajustes cores)
                    {{
                        new Date(rental.data_entrega).toLocaleDateString(
                            'pt-BR',
                            {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                            },
                        )
                    }}
                </p>
            </div>
            <div class="space-y-1">
                <span class="font-bold">Retirada:</span>
                <p class="/90 font-mono">
                    {{
                        new Date(rental.data_recolha).toLocaleDateString(
                            'pt-BR',
                            {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                            },
                        )
                    }}
                </p>
            </div>
<<<<<<< HEAD
            <div class="space-y-1">
                <span class="font-bold">Endereço:</span>
                <p class="/90 mb-2 flex items-start gap-1 font-mono">
=======
            <div class="space-y-1 md:col-span-2">
                <span class="font-bold">Endereço:</span>
                <p class="flex items-start gap-1 font-mono">
>>>>>>> 8c7c2e0 (ajustes cores)
                    <MapPin class="mt-0.5 h-4 w-4 shrink-0" />
                    {{ rental.endereco_entrega || 'Não informado' }}
                </p>

                <!-- SEÇÃO DO MAPA (Botão + Iframe) -->
                <div v-if="rental.endereco_entrega" class="space-y-2">
                    <!-- Botão para abrir no app/site externo -->
                    <a
                        :href="googleMapsUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded bg-primary px-3 py-1.5 text-xs font-bold text-primary-foreground uppercase transition-colors"
                    >
                        <MapPin class="h-3.5 w-3.5" />
                        Abrir no Google Maps
                        <ExternalLink class="h-3 w-3" />
                    </a>

                    <!-- Iframe do Mapa Interativo Gratuito -->
                    <div class="h-52 w-full overflow-hidden rounded">
                        <iframe
                            width="100%"
                            height="100%"
                            style="border: 0"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            :src="googleMapsIframeUrl"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center rounded border p-2">
            <div
                v-if="!rental.items || rental.items.length === 0"
<<<<<<< HEAD
                class="/50 font-mono text-sm"
=======
                class="font-mono text-sm"
>>>>>>> 8c7c2e0 (ajustes cores)
            >
                Nenhum item vinculado a esta locação.
            </div>

            <div v-else class="align-center w-full space-y-5 text-center">
                <div v-if="rentalItemGroups.sets > 0" class="space-y-2">
<<<<<<< HEAD
                    <p class="/80 font-mono text-xs font-bold">
=======
                    <p class="font-mono text-xs font-bold">
>>>>>>> 8c7c2e0 (ajustes cores)
                        {{ rentalItemGroups.sets }}x Conjuntos Completos
                    </p>
                    <div
                        class="flex flex-wrap items-center justify-center gap-2"
                    >
                        <div
                            v-for="set in rentalItemGroups.sets"
                            :key="'set-' + set"
<<<<<<< HEAD
                            class="flex items-center justify-center rounded-sm border"
=======
                            class="flex items-center justify-center rounded border"
>>>>>>> 8c7c2e0 (ajustes cores)
                            title="1 Mesa e 4 Cadeiras"
                        >
                            <div
                                class="grid grid-cols-3 grid-rows-3 place-items-center gap-1"
                            >
                                <Armchair
                                    class="col-start-2 row-start-1 h-4 w-4 text-chart-1"
                                />

                                <Armchair
                                    class="col-start-1 row-start-2 h-4 w-4 -rotate-90 text-chart-3"
                                />

                                <Layout
                                    class="col-start-2 row-start-2 h-6 w-6 text-chart-2"
                                />

                                <Armchair
                                    class="col-start-3 row-start-2 h-4 w-4 rotate-90 text-chart-3"
                                />

                                <Armchair
                                    class="col-start-2 row-start-3 h-4 w-4 -rotate-180 text-chart-3"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-wrap justify-center gap-3 font-mono text-sm"
                >
                    <div
                        v-if="rentalItemGroups.extraTables > 0"
                        class="flex flex-col gap-2 rounded-sm"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <span class="font-bold">Mesas</span>
                            <span class="/80 font-bold"
                                >{{
                                    rentalItemGroups.extraTables
                                }}
                                unidades</span
                            >
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <Layout
                                v-for="n in rentalItemGroups.extraTables"
                                :key="'extra-table-' + n"
                                class="h-6 w-6 shrink-0 text-chart-2 drop-shadow-sm"
                            />
                        </div>
                    </div>

                    <div
                        v-if="rentalItemGroups.extraChairs > 0"
                        class="flex flex-col gap-2 rounded-sm"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <span class="font-bold">Cadeiras</span>
                            <span class="/80 font-bold"
                                >{{
                                    rentalItemGroups.extraChairs
                                }}
                                unidades</span
                            >
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <Armchair
                                v-for="n in rentalItemGroups.extraChairs"
                                :key="'extra-chair-' + n"
                                class="h-6 w-6 shrink-0 text-chart-3 drop-shadow-sm"
                            />
                        </div>
                    </div>

                    <div
                        v-if="rentalItemGroups.coolers > 0"
<<<<<<< HEAD
                        class="border/10 flex flex-col gap-2 rounded-sm border bg-black/40 px-3 py-2"
=======
                        class="border/10 flex flex-col gap-2 rounded-sm border px-3 py-2"
>>>>>>> 8c7c2e0 (ajustes cores)
                    >
                        <div class="flex items-center justify-between gap-4">
                            <span class="font-bold">Coolers</span>
                            <span class="/80 font-bold"
                                >{{ rentalItemGroups.coolers }} unidades</span
                            >
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <Snowflake
                                v-for="n in rentalItemGroups.coolers"
                                :key="'cooler-' + n"
                                class="0 h-6 w-6 shrink-0 drop-shadow-sm"
                            />
                        </div>
                    </div>

                    <div
                        v-for="item in rentalItemGroups.other"
                        :key="item.id"
<<<<<<< HEAD
                        class="border/10 flex flex-col gap-2 rounded-sm border bg-black/40 px-3 py-2"
=======
                        class="border/10 flex flex-col gap-2 rounded-sm border px-3 py-2"
>>>>>>> 8c7c2e0 (ajustes cores)
                    >
                        <div class="flex items-center justify-between gap-4">
                            <span class="font-bold">{{ item.nome }}</span>
                            <span class="/80 font-bold"
                                >{{ item.quantidade }} unidades</span
                            >
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <component
                                :is="item.icone"
                                v-for="n in item.quantidade"
                                :key="item.id + '-icon-' + n"
                                class="h-6 w-6 shrink-0 text-neutral-400"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
