<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import {
    Search,
    FileText,
    Video,
    Presentation as PresentationIcon,
    Calendar,
    User,
    Upload,
    ChevronRight,
    Download,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import type { PageProps } from '@/types';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

interface Category {
    id: number;
    name: string;
    slug: string;
    color: string;
    presentations_count: number;
}

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    filename: string;
    file_size: number;
    file_type: string;
    file_url: string;
    created_at: string;
    category_id: number | null;
    category?: Category;
    uploader: {
        id: number;
        name: string;
    };
    views_count?: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedPresentations {
    data: Presentation[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props extends PageProps {
    presentations: PaginatedPresentations;
    categories: Category[];
}

const { presentations, categories, auth } = defineProps<Props>();

const isAdmin = computed(() => !!auth.user?.isAdmin);
const searchQuery = ref('');
const selectedCategory = ref<number | null>(null);

// ── Video prefetching state ───────────────────────────────────────────────────
const prefetchedVideos = new Set<number>();
const prefetchQueue = ref<number[]>([]);

// ── File type helpers ─────────────────────────────────────────────────────────

const getExt = (p: Presentation) => p.file_type.toLowerCase().trim();

const isPDF = (p: Presentation) =>
    getExt(p) === 'pdf' || p.file_type.includes('pdf') || p.filename.toLowerCase().endsWith('.pdf');

const isVideo = (p: Presentation) =>
    ['mp4', 'mov', 'avi', 'webm'].includes(getExt(p)) || p.file_type.includes('video');

const isOffice = (p: Presentation) =>
    ['ppt', 'pptx', 'pptm', 'ppsx', 'odp'].includes(getExt(p)) ||
    p.file_type.includes('presentation') ||
    p.file_type.includes('powerpoint');

const getFileTypeIcon = (p: Presentation) => {
    if (isVideo(p)) return Video;
    if (isOffice(p)) return PresentationIcon;
    return FileText;
};

const getFileTypeLabel = (p: Presentation) => {
    if (isVideo(p)) return 'Video';
    if (isOffice(p)) return 'Slideshow';
    if (isPDF(p)) return 'PDF';
    return p.file_type.toUpperCase();
};

const getFileTypeBadgeClass = (p: Presentation) => {
    if (isVideo(p)) return 'bg-purple-100 text-purple-700';
    if (isOffice(p)) return 'bg-orange-100 text-orange-700';
    return 'bg-blue-100 text-blue-700';
};

// ── Utils ─────────────────────────────────────────────────────────────────────

const formatFileSize = (bytes: number): string => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes,
        i = 0;
    while (size >= 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    return `${size.toFixed(1)} ${units[i]}`;
};

const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });

// ── Filtering ─────────────────────────────────────────────────────────────────

const filtered = computed(() => {
    return presentations.data.filter((p) => {
        const matchesSearch = !searchQuery.value || p.title.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = selectedCategory.value === null || p.category_id === selectedCategory.value;
        return matchesSearch && matchesCategory;
    });
});

// ── Video Prefetching ─────────────────────────────────────────────────────────

/**
 * Prefetch video metadata to browser cache
 * This makes videos start instantly when clicked
 */
const prefetchVideo = (presentationId: number) => {
    if (prefetchedVideos.has(presentationId)) return;
    
    prefetchedVideos.add(presentationId);
    
    // Create a link element with prefetch
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.as = 'video';
    link.href = `/presentations/${presentationId}/stream`;
    document.head.appendChild(link);
    
    console.log(`[Prefetch] Video ${presentationId} queued for cache`);
};

/**
 * Handle hover - start prefetching immediately
 */
const handleHover = (p: Presentation) => {
    if (isVideo(p)) {
        prefetchVideo(p.id);
    }
};

/**
 * Auto-prefetch visible videos using Intersection Observer
 */
onMounted(() => {
    // Wait a bit for page to settle, then prefetch visible videos
    setTimeout(() => {
        const videoCards = document.querySelectorAll('[data-video-id]');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const videoId = entry.target.getAttribute('data-video-id');
                        if (videoId) {
                            prefetchVideo(parseInt(videoId));
                        }
                    }
                });
            }, {
                rootMargin: '200px' // Start prefetching 200px before video enters viewport
            });
            
            videoCards.forEach(card => observer.observe(card));
        } else {
            // Fallback: prefetch first 3 videos immediately
            const firstVideos = filtered.value.filter(p => isVideo(p)).slice(0, 3);
            firstVideos.forEach(p => prefetchVideo(p.id));
        }
    }, 500);
});
</script>

<template>
    <Head title="Presentations" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">

        <!-- Page header -->
        <div class="border-b border-blue-100 bg-white/80 backdrop-blur-sm">
            <div class="container mx-auto px-4 py-10">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="rounded-full bg-blue-100 p-4">
                            <PresentationIcon class="h-10 w-10 text-blue-600" />
                        </div>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                        Reading Materials
                    </h1>
                    <p class="mt-3 text-base text-gray-600 max-w-xl mx-auto">
                        Browse PDFs, slide decks, and other reading materials.
                        Looking for videos? <Link href="/videos" class="text-violet-600 hover:underline font-medium">Go to Videos →</Link>
                    </p>
                </div>

                <!-- Search -->
                <div class="mt-8 flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input
                            v-model="searchQuery"
                            placeholder="Search presentations…"
                            class="pl-9 bg-white border-gray-200"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 py-10">

            <!-- Empty state -->
            <div v-if="presentations.data.length === 0" class="text-center py-20">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 mb-6">
                    <Upload class="h-10 w-10 text-blue-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No presentations yet</h3>
                <p class="text-gray-500 mb-6">Be the first to share content with the community!</p>
                <Button v-if="isAdmin" as-child>
                    <Link href="/admin/presentations/create">
                        <Upload class="mr-2 h-4 w-4" />
                        Upload First Presentation
                    </Link>
                </Button>
            </div>

            <!-- No search results -->
            <div v-else-if="filtered.length === 0" class="text-center py-20">
                <p class="text-gray-400 text-lg">No presentations match your search.</p>
                <button
                    @click="searchQuery = ''; selectedCategory = null"
                    class="mt-3 text-sm text-blue-600 hover:underline"
                >
                    Clear filters
                </button>
            </div>

            <!-- Grid -->
            <div v-else>
                <p class="text-sm text-gray-500 mb-6">
                    Showing {{ filtered.length }} of {{ presentations.total }} presentations
                </p>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Link
                        v-for="p in filtered"
                        :key="p.id"
                        :href="`/presentations/${p.id}`"
                        :data-video-id="isVideo(p) ? p.id : undefined"
                        class="group flex flex-col bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 overflow-hidden"
                        @mouseenter="handleHover(p)"
                    >
                        <!-- Coloured top strip -->
                        <div
                            class="h-1.5 w-full flex-shrink-0"
                            :style="{ backgroundColor: p.category?.color ?? '#3B82F6' }"
                        />

                        <div class="flex flex-col flex-1 p-4 gap-3">
                            <!-- Icon + type badge -->
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                                    <component :is="getFileTypeIcon(p)" class="h-5 w-5 text-blue-600" />
                                </div>
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0"
                                    :class="getFileTypeBadgeClass(p)"
                                >
                                    {{ getFileTypeLabel(p) }}
                                </span>
                            </div>

                            <!-- Title -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2 group-hover:text-blue-700 transition-colors">
                                    {{ p.title }}
                                </h3>
                                <p v-if="p.description" class="mt-1 text-xs text-gray-500 line-clamp-2">
                                    {{ p.description }}
                                </p>
                            </div>

                            <!-- Category -->
                            <div v-if="p.category" class="flex items-center gap-1.5">
                                <span
                                    class="inline-block h-2 w-2 rounded-full flex-shrink-0"
                                    :style="{ backgroundColor: p.category.color }"
                                />
                                <span class="text-xs text-gray-500 truncate">{{ p.category.name }}</span>
                            </div>

                            <!-- Meta footer -->
                            <div class="border-t border-gray-100 pt-2 flex items-center justify-between text-xs text-gray-400 gap-2">
                                <span class="flex items-center gap-1 truncate">
                                    <User class="h-3 w-3 flex-shrink-0" />
                                    <span class="truncate">{{ p.uploader.name }}</span>
                                </span>
                                <span class="flex items-center gap-1 flex-shrink-0">
                                    <Calendar class="h-3 w-3" />
                                    {{ formatDate(p.created_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Open arrow hint -->
                        <div class="flex items-center justify-end px-4 pb-3 text-xs text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity gap-1">
                            Open
                            <ChevronRight class="h-3.5 w-3.5" />
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="presentations.last_page > 1" class="mt-10 flex items-center justify-center gap-2">
                    <template v-for="(link, i) in presentations.links" :key="i">
                        <Button
                            v-if="link.url"
                            as-child
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                        >
                            <Link :href="link.url" v-html="link.label" />
                        </Button>
                        <span
                            v-else
                            class="px-2 text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
