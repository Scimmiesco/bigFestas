<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import itemsRoutes from '@/routes/items/index';
import type { Team } from '@/types';
import Form from './partials/Form.vue'; // Ajuste o caminho

const props = defineProps<{
    item: any;
    enums?: any;
    itemTypes?: any;
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
                title: 'Editar Item',
                href: '#',
            },
        ],
    }),
});

const combinedEnums = props.enums || {
    item_types: props.itemTypes,
    item_status: props.itemStatuses,
};
</script>

<template>
    <div>
        <Head title="Editar Item" />

        <div class="mx-auto max-w-3xl py-6">
            <Heading
                variant="small"
                title="Editar Item"
                description="Atualize os detalhes do item selecionado abaixo."
            />

            <Form
                :item="props.item"
                :enums="combinedEnums"
                :teamSlug="teamSlug"
            />
        </div>
    </div>
</template>
