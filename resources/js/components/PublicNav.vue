<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Presentation, Menu, BookOpen, Video, Info, HelpCircle } from '@lucide/vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';

const mobileMenuOpen = ref(false);

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

// Scroll-aware navbar
const scrolled = ref(false);
function onScroll() {
    scrolled.value = window.scrollY > 10;
}
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <header
        :class="[
            'sticky top-0 z-50 w-full border-b backdrop-blur-lg transition-all duration-300',
            scrolled
                ? 'border-green-100/60 bg-white/98 shadow-md'
                : 'border-blue-100/40 bg-white/90 shadow-sm',
        ]"
    >
        <div class="container mx-auto px-4">
            <div :class="['flex items-center justify-between gap-4 transition-all duration-300', scrolled ? 'h-14' : 'h-16']">
                <Link
                    href="/"
                    class="flex items-center shrink-0 transition-all duration-300 hover:scale-105"
                >
                    <img
                        src="/logo.png"
                        alt="Gramatecha"
                        class="h-10 w-auto"
                    />
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
                        href="/videos"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-violet-600 text-gray-700 relative group rounded-md hover:bg-violet-50"
                    >
                        Videos
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-violet-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        href="/quizzes"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-blue-600 text-gray-700 relative group rounded-md hover:bg-blue-50"
                    >
                        Quizzes
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        href="/about"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-green-600 text-gray-700 relative group rounded-md hover:bg-green-50"
                    >
                        About
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-green-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                    <Link
                        href="/faq"
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:text-green-600 text-gray-700 relative group rounded-md hover:bg-green-50"
                    >
                        FAQ
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-0 h-0.5 bg-green-600 transition-all duration-300 group-hover:w-3/4"></span>
                    </Link>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <Sheet v-model:open="mobileMenuOpen">
                        <SheetTrigger as-child>
                            <Button variant="ghost" size="icon">
                                <Menu class="h-6 w-6 text-gray-700" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" class="w-80 bg-white">
                            <div class="flex flex-col h-full">
                                <!-- Mobile Menu Header -->
                                <div class="flex items-center justify-between pb-4 border-b border-green-100">
                                    <img src="/logo.png" alt="Gramatecha" class="h-9 w-auto" />
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
                                        <Presentation class="h-5 w-5" />
                                        Presentations
                                    </Link>
                                    <Link
                                        href="/videos"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-violet-50 hover:text-violet-600 rounded-lg transition-colors"
                                    >
                                        <Video class="h-5 w-5" />
                                        Videos
                                    </Link>
                                    <Link
                                        href="/quizzes"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors"
                                    >
                                        <BookOpen class="h-5 w-5" />
                                        Quizzes
                                    </Link>
                                    <Link
                                        href="/about"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition-colors"
                                    >
                                        <Info class="h-5 w-5" />
                                        About
                                    </Link>
                                    <Link
                                        href="/faq"
                                        @click="closeMobileMenu"
                                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-lg transition-colors"
                                    >
                                        <HelpCircle class="h-5 w-5" />
                                        FAQ
                                    </Link>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </div>
    </header>
</template>
