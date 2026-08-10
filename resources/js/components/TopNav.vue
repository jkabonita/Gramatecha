<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    Presentation, 
    LogIn, 
    LayoutGrid, 
    Upload, 
    Menu, 
    X,
    User,
    Settings,
    LogOut,
    FolderOpen
} from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const user = computed(() => page.props.auth?.user);
const mobileMenuOpen = ref(false);

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b border-blue-100 bg-white/95 backdrop-blur-lg shadow-sm"
    >
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between gap-4">
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex items-center gap-2 font-bold text-lg md:text-xl transition-all duration-300 hover:scale-105 text-gray-900 shrink-0"
                >
                    <div class="relative">
                        <Presentation class="h-6 w-6 md:h-7 md:w-7 text-blue-600" />
                    </div>
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent hidden sm:inline">
                        Gramatecha
                    </span>
                </Link>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <Link
                        href="/"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-blue-600 text-gray-700 relative group rounded-md hover:bg-blue-50"
                    >
                        Home
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        href="/presentations"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-blue-600 text-gray-700 relative group rounded-md hover:bg-blue-50"
                    >
                        Presentations
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        v-if="isAuthenticated"
                        href="/admin/dashboard"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-blue-600 text-gray-700 relative group rounded-md hover:bg-blue-50"
                    >
                        Dashboard
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        v-if="isAuthenticated && user?.isAdmin"
                        href="/admin/categories"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-blue-600 text-gray-700 relative group rounded-md hover:bg-blue-50"
                    >
                        Categories
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <!-- Upload Button (Desktop) -->
                    <Button 
                        v-if="isAuthenticated"
                        as-child 
                        variant="outline" 
                        size="sm" 
                        class="hidden md:flex border-blue-200 text-blue-700 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-800 transition-all duration-300"
                    >
                        <Link href="/admin/presentations/create">
                            <Upload class="mr-2 h-4 w-4" />
                            Upload
                        </Link>
                    </Button>

                    <!-- User Menu (Desktop) -->
                    <DropdownMenu v-if="isAuthenticated">
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon" class="hidden md:flex rounded-full hover:bg-blue-50 transition-all duration-300">
                                <Avatar class="h-9 w-9 ring-2 ring-blue-200 hover:ring-blue-400 transition-all">
                                    <AvatarFallback class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-semibold">
                                        {{ getInitials(user?.name || 'User') }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-64 bg-white border-blue-100 shadow-lg">
                            <DropdownMenuLabel>
                                <div class="flex flex-col space-y-1 py-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ user?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ user?.email }}</p>
                                </div>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator class="bg-blue-100" />
                            <DropdownMenuItem as-child>
                                <Link href="/admin/dashboard" class="cursor-pointer flex items-center text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    <LayoutGrid class="mr-2 h-4 w-4" />
                                    Dashboard
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem v-if="user?.isAdmin" as-child>
                                <Link href="/admin/categories" class="cursor-pointer flex items-center text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    <FolderOpen class="mr-2 h-4 w-4" />
                                    Categories
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <Link href="/settings/profile" class="cursor-pointer flex items-center text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    <Settings class="mr-2 h-4 w-4" />
                                    Settings
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator class="bg-blue-100" />
                            <DropdownMenuItem as-child>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="w-full cursor-pointer flex items-center text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors"
                                >
                                    <LogOut class="mr-2 h-4 w-4" />
                                    Logout
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Login Button (Desktop) -->
                    <Button 
                        v-if="!isAuthenticated"
                        as-child 
                        size="sm" 
                        class="hidden md:flex bg-blue-600 hover:bg-blue-700 text-white transition-all duration-300"
                    >
                        <Link href="/login">
                            <LogIn class="mr-2 h-4 w-4" />
                            Login
                        </Link>
                    </Button>

                    <!-- Mobile Menu Button -->
                    <Sheet v-model:open="mobileMenuOpen">
                        <SheetTrigger as-child>
                            <Button variant="ghost" size="icon" class="md:hidden">
                                <Menu class="h-6 w-6 text-gray-700" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-80 bg-white">
                            <div class="flex flex-col h-full">
                                <!-- Mobile Menu Header -->
                                <div class="flex items-center justify-between pb-4 border-b border-blue-100">
                                    <div class="flex items-center gap-2">
                                        <Presentation class="h-6 w-6 text-blue-600" />
                                        <span class="font-bold text-lg bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                            Gramatecha
                                        </span>
                                    </div>
                                </div>

                                <!-- Mobile Navigation -->
                                <nav class="flex flex-col gap-1 py-4">
                                    <Link
                                        href="/"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                    >
                                        <Presentation class="h-5 w-5" />
                                        Home
                                    </Link>
                                    <Link
                                        href="/presentations"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                    >
                                        <FolderOpen class="h-5 w-5" />
                                        Presentations
                                    </Link>
                                    <Link
                                        v-if="isAuthenticated"
                                        href="/admin/dashboard"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                    >
                                        <LayoutGrid class="h-5 w-5" />
                                        Dashboard
                                    </Link>
                                    <Link
                                        v-if="isAuthenticated && user?.isAdmin"
                                        href="/admin/categories"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                    >
                                        <FolderOpen class="h-5 w-5" />
                                        Categories
                                    </Link>
                                </nav>

                                <!-- Mobile User Section -->
                                <div class="mt-auto pt-4 border-t border-blue-100">
                                    <template v-if="isAuthenticated">
                                        <!-- User Info -->
                                        <div class="flex items-center gap-3 px-4 py-3 mb-2">
                                            <Avatar class="h-10 w-10 ring-2 ring-blue-200">
                                                <AvatarFallback class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-semibold">
                                                    {{ getInitials(user?.name || 'User') }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div class="flex flex-col flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ user?.name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
                                            </div>
                                        </div>

                                        <!-- Upload Button -->
                                        <Link
                                            href="/admin/presentations/create"
                                            @click="closeMobileMenu"
                                            class="flex items-center justify-center gap-2 mx-4 mb-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                                        >
                                            <Upload class="h-4 w-4" />
                                            Upload Presentation
                                        </Link>

                                        <!-- Settings & Logout -->
                                        <div class="flex flex-col gap-1">
                                            <Link
                                                href="/settings/profile"
                                                @click="closeMobileMenu"
                                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                            >
                                                <Settings class="h-5 w-5" />
                                                Settings
                                            </Link>
                                            <Link
                                                href="/logout"
                                                method="post"
                                                as="button"
                                                @click="closeMobileMenu"
                                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors w-full text-left"
                                            >
                                                <LogOut class="h-5 w-5" />
                                                Logout
                                            </Link>
                                        </div>
                                    </template>

                                    <template v-else>
                                        <Link
                                            href="/login"
                                            @click="closeMobileMenu"
                                            class="flex items-center justify-center gap-2 mx-4 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                                        >
                                            <LogIn class="h-4 w-4" />
                                            Login
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </div>
    </header>
</template>
