<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Presentation, Upload, LogIn } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Presentations',
            href: '/presentations',
            icon: Presentation,
        },
    ];

    // Add Dashboard and Upload for authenticated users
    if (isAuthenticated.value) {
        items.unshift({
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        });
        items.push({
            title: 'Upload',
            href: '/admin/presentations/create',
            icon: Upload,
        });
    } else {
        // Add Login for non-authenticated users
        items.push({
            title: 'Login',
            href: '/login',
            icon: LogIn,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
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
