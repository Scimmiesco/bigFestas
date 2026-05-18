<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import RentalDetails from '@/components/RentalDetails.vue';

// 1. CORREÇÃO: Atribuir o defineProps a uma constante para poder usar no JavaScript
const props = defineProps<{
    rental: any;
}>();

// 2. CORREÇÃO: Usar 'props.rental.id' em vez de apenas 'rental.id'
const titulo = computed(() => {
    return 'Locação #' + props.rental?.id;
});

defineOptions({
    // Capturamos os props da página através do argumento da função do layout
    layout: (pageProps: { rental?: any }) => ({
        breadcrumbs: [
            {
                // Acessa diretamente o id do rental que veio do servidor
                title: pageProps.rental
                    ? `Locação #${pageProps.rental.id}`
                    : 'Locação',
                href: '#',
            },
        ],
    }),
});
</script>

<template>
    <!-- 3. MELHORIA: Usando a sua computed property 'titulo' que estava ociosa -->
    <Head :title="titulo" />

    <div class="mx-auto max-w-4xl px-4 py-8">
        <div class="rounded-md border border-amber-100 p-2 shadow-sm">
            <RentalDetails :rental="rental" />
        </div>
    </div>
</template>
