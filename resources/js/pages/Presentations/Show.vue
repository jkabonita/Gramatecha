<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowLeft,
    Calendar,
    Download,
    Edit,
    ExternalLink,
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
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({ layout: PublicLayout });

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

const { presentation, auth } = defineProps<Props>();

const isAdmin = computed(() => !!auth.user?.isAdmin);
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const videoElement = ref<HTMLVideoElement | null>(null);

// Back button — goes to /videos for video files, /presentations for everything else
const backHref = computed(() => isVideo.value ? '/videos' : '/presentations');
const backLabel = computed(() => isVideo.value ? 'Back to Videos' : 'Back to Presentations');

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

/**
 * The best URL for inline viewing:
 *  - PDFs  → the /stream route (served by Laravel, works on localhost)
 *  - Others → direct public file_url
 */
const viewUrl = computed(() => presentation.inline_view_url || presentation.file_url);

/** Download URL — always the /download route. */
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

    <div class="min-h-screen bg-white">
        <!-- ── Header ──────────────────────────────────────────────────────── -->
        <div class="border-b bg-gray-50 px-4 py-4 sm:px-6">
            <!-- Back link -->
            <div class="mb-3">
                <Button as-child variant="ghost" size="sm">
                    <Link :href="backHref">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        {{ backLabel }}
                    </Link>
                </Button>
            </div>

            <!-- Title + meta + actions in one bar -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl lg:text-3xl leading-tight">
                        {{ presentation.title }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5"><User class="h-3.5 w-3.5" />{{ presentation.uploader.name }}</span>
                        <span class="flex items-center gap-1.5"><Calendar class="h-3.5 w-3.5" />{{ formatDate(presentation.created_at) }}</span>
                        <span class="flex items-center gap-1.5"><FileText class="h-3.5 w-3.5" />{{ formatFileSize(presentation.file_size) }}</span>
                        <span v-if="presentation.views_count" class="flex items-center gap-1.5"><Eye class="h-3.5 w-3.5" />{{ presentation.views_count.toLocaleString() }} views</span>
                    </div>
                </div>
                <div v-if="isAdmin" class="flex flex-wrap gap-2 flex-shrink-0">
                    <Button as-child variant="outline" size="sm">
                        <Link :href="`/admin/presentations/${presentation.id}/edit`">
                            <Edit class="mr-1.5 h-3.5 w-3.5" />Edit
                        </Link>
                    </Button>
                    <Button variant="destructive" size="sm" @click="showDeleteDialog = true">
                        <Trash2 class="mr-1.5 h-3.5 w-3.5" />Delete
                    </Button>
                </div>
            </div>
        </div>

        <!-- ── Viewer — full width, no max-w constraint ─────────────────── -->
        <div class="px-2 py-4 sm:px-4">
            <!-- Description -->
            <div v-if="presentation.description" class="mb-4 rounded-lg border bg-white p-4 sm:p-5">
                <h2 class="mb-1 text-sm font-semibold text-gray-700 uppercase tracking-wide">Description</h2>
                <p class="whitespace-pre-wrap text-gray-600 text-sm sm:text-base">{{ presentation.description }}</p>
            </div>

            <!-- ── Viewer ───────────────────────────────────────────── -->
            <div class="overflow-hidden rounded-lg border bg-white">
                <div v-if="isPDF">
                    <PdfPageViewer :stream-url="viewUrl" :download-url="downloadUrl" />
                </div>
                <div v-else-if="isVideo" class="bg-black">
                    <video
                        ref="videoElement"
                        :src="presentation.file_url"
                        controls
                        playsinline
                        preload="metadata"
                        class="w-full"
                        style="max-height: 80vh;"
                        crossorigin="anonymous"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div v-else-if="isOffice">
                    <PptxSlideViewer :presentation-id="presentation.id" :download-url="downloadUrl" :stream-url="viewUrl" />
                </div>
                <div v-else class="p-8 text-center">
                    <FileText class="mx-auto mb-3 h-12 w-12 text-gray-300" />
                    <p class="mb-4 text-gray-500">Preview not available for <strong>{{ presentation.file_type.toUpperCase() }}</strong> files.</p>
                    <Button as-child>
                        <a :href="downloadUrl" download><Download class="mr-2 h-4 w-4" />Download File</a>
                    </Button>
                </div>
            </div>

            <!-- ── File info ────────────────────────────────────────── -->
            <div class="mt-4 rounded-lg border bg-gray-50 p-4">
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">File</dt>
                        <dd class="mt-0.5 truncate text-sm text-gray-900" :title="presentation.filename">{{ presentation.filename }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Type</dt>
                        <dd class="mt-0.5 text-sm text-gray-900 uppercase">{{ presentation.file_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Size</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ formatFileSize(presentation.file_size) }}</dd>
                    </div>
                    <div v-if="presentation.category">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Category</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ presentation.category.name }}</dd>
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
