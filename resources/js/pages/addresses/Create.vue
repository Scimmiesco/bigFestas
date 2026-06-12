<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

import Form from './partials/Form.vue';

const page = usePage();
const teamSlug = page.props.currentTeam?.slug as string;

const props = defineProps<{
    clients: any;
    enums: any;
}>();
</script>

<template>
    <div class="page-container">
        <Head title="Adicionar Novo Endereço" />

        <div class="page-header">
            <Heading
                variant="default"
                title="Adicionar Endereço"
                description="Preencha os detalhes abaixo para registrar um novo endereço."
            />
        </div>
        <Form :teamSlug="teamSlug" :clients="clients" :enums="enums" />
    </div>
</template>
