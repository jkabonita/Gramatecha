<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { Upload, Search, FileText, Video, Presentation as PresentationIcon } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import type { PageProps } from '@/types';

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    filename: string;
    file_size: number;
    file_type: string;
    cloudinary_url: string;
    cloudinary_public_id: string;
    created_at: string;
    uploader: {
        id: number;
        name: string;
        email: string;
    };
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
}

const { presentations, auth } = defineProps<Props>();

const isAdmin = computed(() => !!auth.user);
const isVisible = ref(false);
const searchQuery = ref('');
const selectedPresentation = ref<number | null>(
    presentations.data.length > 0 ? presentations.data[0].id : null
);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});

const currentPresentation = computed(() => {
    return presentations.data.find((p) => p.id === selectedPresentation.value);
});

const getFileTypeIcon = (fileType: string) => {
    if (fileType.includes('video')) return Video;
    if (fileType.includes('presentation') || fileType.includes('powerpoint'))
        return PresentationIcon;
    return FileText;
};

const formatFileSize = (bytes: number): string => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;

    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }

    return `${size.toFixed(2)} ${units[unitIndex]}`;
};

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const selectPresentation = (id: number) => {
    selectedPresentation.value = id;
    router.visit(`/presentations/${id}`, { preserveScroll: true });
};

const filteredPresentations = computed(() => {
    if (!searchQuery.value) return presentations.data;
    return presentations.data.filter((p) =>
        p.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});
</script>

<template>
    <Head title="Presentations" />

    <div class="flex h-[calc(100vh-4rem)]">
        <!-- Sidebar - Presentations List -->
        <aside class="w-80 border-r border-sidebar-border/70 bg-muted/30">
            <div class="flex h-full flex-col">
                <!-- Search Header -->
                <div class="border-b border-sidebar-border/70 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Presentations</h2>
                        <span class="text-xs text-muted-foreground">
                            {{ presentations.total }} files
                        </span>
                    </div>
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            placeholder="Search presentations..."
                            class="pl-9"
                        />
                    </div>
                    <Button v-if="isAdmin" as-child class="w-full" size="sm">
                        <Link href="/admin/presentations/create">
                            <Upload class="mr-2 h-4 w-4" />
                            Upload New
                        </Link>
                    </Button>
                </div>

                <!-- Presentations List -->
                <ScrollArea class="flex-1">
                    <div class="space-y-1 p-2">
                        <button
                            v-for="presentation in filteredPresentations"
                            :key="presentation.id"
                            @click="selectPresentation(presentation.id)"
                            :class="[
                                'w-full rounded-lg p-3 text-left transition-all hover:bg-accent',
                                selectedPresentation === presentation.id
                                    ? 'bg-accent border border-primary'
                                    : 'border border-transparent',
                            ]"
                        >
                            <div class="flex items-start gap-3">
                                <component
                                    :is="getFileTypeIcon(presentation.file_type)"
                                    class="h-5 w-5 flex-shrink-0 text-primary"
                                />
                                <div class="flex-1 overflow-hidden">
                                    <h3
                                        class="truncate text-sm font-medium"
                                        :class="
                                            selectedPresentation === presentation.id
                                                ? 'text-primary'
                                                : 'text-foreground'
                                        "
                                    >
                                        {{ presentation.title }}
                                    </h3>
                                    <p class="text-xs text-muted-foreground">
                                        {{ presentation.uploader.name }} •
                                        {{ formatDate(presentation.created_at) }}
                                    </p>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        {{ formatFileSize(presentation.file_size) }}
                                    </p>
                                </div>
                            </div>
                        </button>

                        <div
                            v-if="filteredPresentations.length === 0"
                            class="p-8 text-center text-sm text-muted-foreground"
                        >
                            No presentations found
                        </div>
                    </div>
                </ScrollArea>

                <!-- Pagination -->
                <div
                    v-if="presentations.last_page > 1"
                    class="border-t border-sidebar-border/70 p-4"
                >
                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>
                            Page {{ presentations.current_page }} of {{ presentations.last_page }}
                        </span>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in presentations.links">
                                <Button
                                    v-if="link.url && (index === 0 || index === presentations.links.length - 1)"
                                    :key="`btn-${index}`"
                                    as-child
                                    variant="ghost"
                                    size="sm"
                                    class="h-7 w-7 p-0"
                                >
                                    <Link :href="link.url" v-html="link.label" />
                                </Button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-auto">
            <div v-if="presentations.data.length === 0" class="flex h-full items-center justify-center">
                <div class="text-center space-y-6 p-8">
                    <div
                        class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-primary/10"
                    >
                        <Upload class="h-12 w-12 text-primary" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-foreground mb-2">
                            No presentations yet
                        </h3>
                        <p class="text-muted-foreground">
                            Be the first to share amazing content with the community!
                        </p>
                    </div>
                    <Button v-if="isAdmin" as-child size="lg">
                        <Link href="/admin/presentations/create">
                            <Upload class="mr-2 h-5 w-5" />
                            Upload Your First Presentation
                        </Link>
                    </Button>
                </div>
            </div>

            <div v-else-if="currentPresentation" class="h-full">
                <iframe
                    :src="`/presentations/${currentPresentation.id}`"
                    class="h-full w-full border-0"
                    title="Presentation Viewer"
                />
            </div>
        </main>
    </div>
</template>
