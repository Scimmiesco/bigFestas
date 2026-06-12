<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';

import Form from './partials/Form.vue';

const page = usePage();
const teamSlug = page.props.currentTeam?.slug as string;

const props = defineProps<{
    client: any;
    enums?: any;
}>();
</script>

<template>
    <div class="page-container">
        <Head title="Editar Cliente" />

        <div class="page-header">
            <Heading
                variant="default"
                title="Editar Cliente"
                description="Atualize os detalhes do cliente abaixo."
            />
        </div>
        <Form :teamSlug="teamSlug" :client="client" :enums="enums" />
    </div>
</template>
