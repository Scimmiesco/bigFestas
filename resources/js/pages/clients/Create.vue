<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

import Form from './partials/Form.vue';

const page = usePage();
const teamSlug = page.props.currentTeam?.slug as string;
</script>

<template>
    <div class="page-container">
        <Head title="Adicionar Novo Cliente" />

        <div class="page-header">
            <Heading
                variant="default"
                title="Adicionar Clientes"
                description="Preencha os detalhes abaixo para registrar um novo cliente."
            />
        </div>
        <Form :teamSlug="teamSlug" :client="''" :enums="''" />
    </div>
</template>
