<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StockResume from '@/components/StockResume.vue';
import dashboard from '@/routes/dashboard/index';
import type { Team } from '@/types';

defineProps<{
    stockResumeData: string[];
    fkasasp: string;

    errors?: any;
    name?: string;
    enums?: any;
    auth?: any;
    sidebarOpen?: boolean;
    currentTeam?: any;
    teams?: any;
}>();

defineOptions({
    layout: (pageProps: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: pageProps.currentTeam
                    ? dashboard.index.url(pageProps.currentTeam.slug)
                    : dashboard.index.url('/'),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <StockResume></StockResume>

        {{ stockResumeData }}
        {{ fkasasp }}
    </div>
</template>
