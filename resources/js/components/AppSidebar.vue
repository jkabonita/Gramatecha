<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Presentation, Upload, LogIn, FolderTree, ClipboardList, X } from '@lucide/vue';
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
    useSidebar,
} from '@/components/ui/sidebar';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const { isMobile, setOpenMobile, toggleSidebar } = useSidebar();

// Check if current URL is in admin context (includes /admin/* and /dashboard)
const isAdminContext = computed(() => {
    const url = page.url;
    return url.startsWith('/admin') || url.startsWith('/dashboard');
});

const closeSidebar = () => {
    if (isMobile.value) {
        setOpenMobile(false);
    } else {
        toggleSidebar();
    }
};

const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [];

    if (isAdminContext.value) {
        // Admin navigation (when on /admin/* pages)
        items.push({
            title: 'Dashboard',
            href: '/admin/dashboard',
            icon: LayoutGrid,
        });
        items.push({
            title: 'Presentations',
            href: '/admin/presentations',
            icon: Presentation,
        });
        items.push({
            title: 'Categories',
            href: '/admin/categories',
            icon: FolderTree,
        });
        items.push({
            title: 'Quiz Attempts',
            href: '/admin/quiz-attempts',
            icon: ClipboardList,
        });
        items.push({
            title: 'Upload',
            href: '/admin/presentations/create',
            icon: Upload,
        });
    } else {
        // Public navigation (when on public pages)
        items.push({
            title: 'Presentations',
            href: '/presentations',
            icon: Presentation,
        });
        
        if (isAuthenticated.value) {
            // Add Upload for authenticated users
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
    }

    return items;
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <div class="flex items-center justify-between">
                <SidebarMenu class="flex-1">
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link href="/">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 md:hidden"
                    @click="closeSidebar"
                    aria-label="Close sidebar"
                >
                    <X class="h-5 w-5" />
                </Button>
            </div>
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
