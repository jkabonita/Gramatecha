<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowLeft,
    Calendar,
    Download,
    Edit,
    Eye,
    FileText,
    Trash2,
    User,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { toast } from 'vue-sonner';
import type { PageProps } from '@/types';
import PptxSlideViewer from '@/components/PptxSlideViewer.vue';
import PdfPageViewer from '@/components/PdfPageViewer.vue';

// ── Types ────────────────────────────────────────────────────────────────────

interface Uploader {
    id: number;
    name: string;
    email: string;
}

interface Category {
    id: number;
    name: string;
}

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    filename: string;
    file_size: number;
    file_type: string;
    file_path: string;
    file_url: string;
    views_count: number;
    inline_view_url?: string;
    created_at: string;
    uploader: Uploader;
    category: Category | null;
}

interface Props extends PageProps {
    presentation: Presentation;
}

// ── Props & state ─────────────────────────────────────────────────────────────

const { presentation } = defineProps<Props>();

const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const videoElement = ref<HTMLVideoElement | null>(null);

// Toggle play/pause when video is clicked (but not controls)
const togglePlayPause = (event: MouseEvent) => {
    if (!videoElement.value) return;
    
    // Only toggle if clicking on the video itself, not the controls
    const target = event.target as HTMLElement;
    if (target.tagName === 'VIDEO') {
        if (videoElement.value.paused) {
            videoElement.value.play();
        } else {
            videoElement.value.pause();
        }
    }
};

// ── Helpers ───────────────────────────────────────────────────────────────────

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
        month: 'long',
        day: 'numeric',
    });
};

// ── File type detection ───────────────────────────────────────────────────────

const ext = computed(() => presentation.file_type.toLowerCase().trim());

const isPDF = computed(() =>
    ext.value === 'pdf' ||
    presentation.file_type.includes('pdf') ||
    presentation.filename.toLowerCase().endsWith('.pdf'),
);

const isPowerPoint = computed(() =>
    ['ppt', 'pptx', 'pptm', 'ppsx'].includes(ext.value) ||
    presentation.file_type.includes('presentation') ||
    presentation.file_type.includes('powerpoint') ||
    presentation.file_type.includes('officedocument') ||
    /\.(ppt|pptx|pptm|ppsx)$/i.test(presentation.filename),
);

const isODP = computed(() =>
    ext.value === 'odp' ||
    presentation.file_type.includes('opendocument'),
);

const isVideo = computed(() =>
    ['mp4', 'mov', 'avi', 'webm'].includes(ext.value) ||
    presentation.file_type.includes('video'),
);

const isOffice = computed(() => isPowerPoint.value || isODP.value);

// ── URLs ──────────────────────────────────────────────────────────────────────

const viewUrl = computed(() => presentation.inline_view_url || presentation.file_url);
const downloadUrl = computed(() => `/presentations/${presentation.id}/download`);

// ── Actions ───────────────────────────────────────────────────────────────────

const handleDelete = () => {
    isDeleting.value = true;

    router.delete(`/admin/presentations/${presentation.id}`, {
        onSuccess: () => {
            toast.success('Presentation deleted successfully');
        },
        onError: () => {
            toast.error('Failed to delete presentation');
            isDeleting.value = false;
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="presentation.title" />

    <div class="min-h-screen bg-background">
        <!-- ── Header ──────────────────────────────────────────────────────── -->
        <div class="border-b border-sidebar-border/70 bg-muted/20 px-4 py-6 dark:border-sidebar-border sm:px-6 lg:px-8">
            <div class="mx-auto max-w-5xl">
                <!-- Back link -->
                <div class="mb-4">
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/admin/presentations">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Presentations
                        </Link>
                    </Button>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                    {{ presentation.title }}
                </h1>

                <!-- Meta -->
                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                    <div class="flex items-center gap-2">
                        <User class="h-4 w-4" />
                        <span>{{ presentation.uploader.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Calendar class="h-4 w-4" />
                        <span>{{ formatDate(presentation.created_at) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <FileText class="h-4 w-4" />
                        <span>{{ formatFileSize(presentation.file_size) }}</span>
                    </div>
                    <div v-if="presentation.views_count !== undefined" class="flex items-center gap-2">
                        <Eye class="h-4 w-4" />
                        <span>{{ presentation.views_count.toLocaleString() }} views</span>
                    </div>
                </div>

                <!-- Admin actions -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <Button as-child variant="outline" size="sm">
                        <Link :href="`/admin/presentations/${presentation.id}/edit`">
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button variant="destructive" size="sm" @click="showDeleteDialog = true">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>
        </div>

        <!-- ── Content ─────────────────────────────────────────────────────── -->
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

            <!-- Description -->
            <div
                v-if="presentation.description"
                class="mb-8 rounded-lg border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
            >
                <h2 class="mb-2 text-lg font-semibold text-foreground">Description</h2>
                <p class="whitespace-pre-wrap text-muted-foreground">{{ presentation.description }}</p>
            </div>

            <!-- ── Viewer ──────────────────────────────────────────────────── -->
            <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-card dark:border-sidebar-border">

                <!-- PDF: page-by-page viewer using PDF.js -->
                <div v-if="isPDF">
                    <PdfPageViewer
                        :stream-url="viewUrl"
                        :download-url="downloadUrl"
                    />
                </div>

                <!-- Video: Native HTML5 video player with optimized settings and caching -->
                <div v-else-if="isVideo" class="bg-black">
                    <video
                        ref="videoElement"
                        :src="presentation.file_url"
                        controls
                        playsinline
                        preload="metadata"
                        class="w-full cursor-pointer"
                        style="max-height: 80vh;"
                        crossorigin="anonymous"
                        @click="togglePlayPause"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Office (PPTX / ODP): Custom slide viewer -->
                <div v-else-if="isOffice">
                    <PptxSlideViewer
                        :presentation-id="presentation.id"
                        :download-url="downloadUrl"
                        :stream-url="viewUrl"
                    />
                </div>

                <!-- Fallback: any other file type -->
                <div v-else class="p-8 text-center">
                    <FileText class="mx-auto mb-3 h-12 w-12 text-muted-foreground/50" />
                    <p class="mb-4 text-muted-foreground">
                        Preview not available for <strong>{{ presentation.file_type.toUpperCase() }}</strong> files.
                    </p>
                    <Button as-child>
                        <a :href="downloadUrl" download>
                            <Download class="mr-2 h-4 w-4" />
                            Download File
                        </a>
                    </Button>
                </div>
            </div>

            <!-- ── File info card ──────────────────────────────────────────── -->
            <div class="mt-6 rounded-lg border border-sidebar-border/70 bg-muted/10 p-4 dark:border-sidebar-border">
                <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">File Name</dt>
                        <dd class="mt-1 truncate text-sm text-foreground" :title="presentation.filename">
                            {{ presentation.filename }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">File Type</dt>
                        <dd class="mt-1 text-sm text-foreground uppercase">{{ presentation.file_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">File Size</dt>
                        <dd class="mt-1 text-sm text-foreground">{{ formatFileSize(presentation.file_size) }}</dd>
                    </div>
                    <div v-if="presentation.category">
                        <dt class="text-sm font-medium text-muted-foreground">Category</dt>
                        <dd class="mt-1 text-sm text-foreground">{{ presentation.category.name }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- ── Delete confirmation dialog ─────────────────────────────────────── -->
    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete Presentation</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete "{{ presentation.title }}"?
                    This action cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showDeleteDialog = false" :disabled="isDeleting">
                    Cancel
                </Button>
                <Button variant="destructive" @click="handleDelete" :disabled="isDeleting">
                    {{ isDeleting ? 'Deleting...' : 'Delete' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
