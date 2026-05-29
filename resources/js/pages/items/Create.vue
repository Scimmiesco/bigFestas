<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

import itemsRoutes from '@/routes/items/index';
import type { Team } from '@/types';
import Form from './partials/Form.vue'; // Ajuste o caminho conforme onde você salvou

const props = defineProps<{
    enums?: any;
    itemTypes?: any; // Algumas controllers mandam solto
    itemStatuses?: any;
}>();

const page = usePage();
const teamSlug = page.props.currentTeam?.slug as string;

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Estoque',
                href: pageProps.currentTeam
                    ? itemsRoutes.index.url(pageProps.currentTeam.slug)
                    : '/',
            },
            {
                title: 'Novo Item',
                href: '#',
            },
        ],
    }),
});

// Consolida os enums caso venham separados da controller
const combinedEnums = props.enums || {
    item_types: props.itemTypes,
    item_status: props.itemStatuses,
};
</script>

<template>
    <div class="page-container">
        <Head title="Adicionar Novo Item" />

        <div class="page-header">
            <Heading
                variant="default"
                title="Adicionar Item ao Estoque"
                description="Preencha os detalhes abaixo para registrar um novo item."
            />
        </div>

        <Form :enums="combinedEnums" :teamSlug="teamSlug" />
    </div>
</template>
