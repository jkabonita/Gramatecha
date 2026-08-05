<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Presentation, LogIn, LayoutGrid, Upload } from '@lucide/vue';
import { computed } from 'vue';
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

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const user = computed(() => page.props.auth?.user);

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};
</script>

<template>
    <header
        class="sticky top-0 z-50 w-full border-b border-blue-100 bg-white/80 backdrop-blur-md transition-all duration-300"
    >
        <div class="container flex h-16 items-center justify-between">
            <!-- Logo -->
            <Link
                href="/"
                class="flex items-center gap-2 font-bold text-xl transition-all duration-300 hover:scale-105 text-gray-900"
            >
                <div class="relative">
                    <Presentation class="h-6 w-6 text-blue-500 animate-pulse" />
                    <div class="absolute inset-0 bg-blue-400 blur-lg opacity-50 animate-ping"></div>
                </div>
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Gramatecha</span>
            </Link>

            <!-- Center Navigation -->
            <nav class="flex items-center gap-6">
                <Link
                    href="/presentations"
                    class="text-sm font-medium transition-all duration-300 hover:text-blue-500 hover:scale-105 text-gray-700 relative group"
                >
                    Presentations
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-500 transition-all duration-300 group-hover:w-full"></span>
                </Link>
                <Link
                    v-if="isAuthenticated"
                    href="/dashboard"
                    class="text-sm font-medium transition-all duration-300 hover:text-blue-500 hover:scale-105 text-gray-700 relative group"
                >
                    Dashboard
                    <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-500 transition-all duration-300 group-hover:w-full"></span>
                </Link>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <template v-if="isAuthenticated">
                    <Button as-child variant="outline" size="sm" class="border-blue-200 text-gray-700 hover:bg-blue-50 hover:border-blue-300 transition-all duration-300 hover:scale-105">
                        <Link href="/admin/presentations/create">
                            <Upload class="mr-2 h-4 w-4" />
                            Upload
                        </Link>
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon" class="rounded-full hover:bg-blue-50 transition-all duration-300 hover:scale-110">
                                <Avatar class="h-8 w-8 ring-2 ring-blue-200 ring-offset-2">
                                    <AvatarFallback class="bg-gradient-to-br from-blue-400 to-indigo-500 text-white">
                                        {{ getInitials(user?.name || 'U') }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56 bg-white/95 backdrop-blur-md border-blue-100">
                            <DropdownMenuLabel>
                                <div class="flex flex-col space-y-1">
                                    <p class="text-sm font-medium text-gray-900">{{ user?.name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ user?.email }}
                                    </p>
                                </div>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator class="bg-blue-100" />
                            <DropdownMenuItem as-child>
                                <Link href="/dashboard" class="cursor-pointer text-gray-700 hover:bg-blue-50 transition-colors">
                                    <LayoutGrid class="mr-2 h-4 w-4" />
                                    Dashboard
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <Link href="/settings/profile" class="cursor-pointer text-gray-700 hover:bg-blue-50 transition-colors">
                                    Settings
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator class="bg-blue-100" />
                            <DropdownMenuItem as-child>
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="w-full cursor-pointer text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    Logout
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </template>

                <template v-else>
                    <Button as-child variant="ghost" size="sm" class="text-gray-700 hover:bg-blue-50 transition-all duration-300 hover:scale-105">
                        <Link href="/login">
                            <LogIn class="mr-2 h-4 w-4" />
                            Login
                        </Link>
                    </Button>
                </template>
            </div>
        </div>
    </header>
</template>
