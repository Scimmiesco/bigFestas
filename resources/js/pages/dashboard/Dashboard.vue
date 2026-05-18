<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DeliveryPlanner from '@/components/DeliveryPlanner.vue';
import PaymentResume from '@/components/PaymentResume.vue';
import StockResume from '@/components/StockResume.vue';
import dashboard from '@/routes/dashboard/index';
import type { Team } from '@/types';

defineProps<{
    stockResumeData: any;
    paymentData: any;

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
    <div class="flex flex-1 flex-col gap-2 px-2">
        <PaymentResume :data="paymentData"></PaymentResume>
        <DeliveryPlanner
            :agenda-semanal="stockResumeData.agenda_semanal"
            :team-slug="currentTeam.slug"
        />
        <StockResume :data="stockResumeData" />
    </div>
</template>
