<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    LayoutGrid,
    Boxes,
    Truck,
    Wallet,
    Users,
    MapPinHouse,
} from 'lucide-vue-next';
import { computed, ref, onMounted } from 'vue'; // 👈 Adicionados ref e onMounted aqui
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import addresses from '@/routes/addresses/index';
import client from '@/routes/clients/index';
import dashboard from '@/routes/dashboard/index';
import item from '@/routes/items/index';
import payment from '@/routes/payment/index';
import rentals from '@/routes/rentals/index';
import type { NavItem } from '@/types';

// 👇 Controle de hidratação (SSR vs Client)
const isMounted = ref(false);

onMounted(() => {
    isMounted.value = true;
});

const page = usePage();

const dashboardUrl = computed(() =>
    page.props.currentTeam
        ? dashboard.index(page.props.currentTeam.slug).url
        : '/',
);

const itemUrl = computed(() =>
    page.props.currentTeam ? item.index(page.props.currentTeam.slug).url : '/',
);

const rentalsUrl = computed(() =>
    page.props.currentTeam
        ? rentals.index(page.props.currentTeam.slug).url
        : '/',
);

const paymentsUrl = computed(() =>
    page.props.currentTeam
        ? payment.index(page.props.currentTeam.slug).url
        : '/',
);

const clientsUrl = computed(() =>
    page.props.currentTeam
        ? client.index(page.props.currentTeam.slug).url
        : '/',
);

const addressesUrl = computed(() =>
    page.props.currentTeam
        ? addresses.index(page.props.currentTeam.slug).url
        : '/',
);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboardUrl.value,
        icon: LayoutGrid,
    },
    {
        title: 'Estoque',
        href: itemUrl.value,
        icon: Boxes,
    },
    {
        title: 'Locações',
        href: rentalsUrl.value,
        icon: Truck,
    },
    {
        title: 'Pagamentos',
        href: paymentsUrl.value,
        icon: Wallet,
    },
    {
        title: 'Clientes',
        href: clientsUrl.value,
        icon: Users,
    },
    {
        title: 'Endereços',
        href: addressesUrl.value,
        icon: MapPinHouse,
    },
]);

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <!-- 👇 O v-if garante que a Sidebar só tente desenhar a tela quando o Vue já souber o tamanho do navegador -->
    <Sidebar v-if="isMounted" collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
