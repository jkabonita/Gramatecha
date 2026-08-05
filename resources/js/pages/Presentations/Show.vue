<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import {
    ArrowLeft,
    Calendar,
    Edit,
    ExternalLink,
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

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    filename: string;
    file_size: number;
    file_type: string;
    cloudinary_url: string;
    cloudinary_public_id: string;
    inline_view_url?: string;
    created_at: string;
    uploader: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props extends PageProps {
    presentation: Presentation;
}

const { presentation, auth } = defineProps<Props>();

const isAdmin = computed(() => !!auth.user);
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const viewerContainer = ref<HTMLDivElement>();

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

const isVideo = computed(() =>
    presentation.file_type.includes('video') ||
    ['mp4', 'mov', 'avi', 'webm'].includes(presentation.file_type.toLowerCase())
);

const isPDF = computed(() =>
    presentation.file_type.includes('pdf') ||
    presentation.file_type.toLowerCase() === 'pdf' ||
    presentation.filename.toLowerCase().endsWith('.pdf')
);

const isPowerPoint = computed(() =>
    presentation.file_type.includes('presentation') ||
    presentation.file_type.includes('powerpoint') ||
    presentation.file_type.includes('officedocument') ||
    ['ppt', 'pptx', 'pptm', 'ppsx'].includes(presentation.file_type.toLowerCase()) ||
    presentation.filename.toLowerCase().match(/\.(ppt|pptx|pptm|ppsx)$/)
);

const cloudName = 'io7qeps0';

// Get the best URL for viewing (inline_view_url preferred, fallback to cloudinary_url)
const viewUrl = computed(() => presentation.inline_view_url || presentation.cloudinary_url);

// Generate Cloudinary viewer URL for PDFs
const pdfViewerUrl = computed(() => {
    if (isPDF.value) {
        return viewUrl.value;
    }
    return null;
});

// Generate Google Docs Viewer URL as fallback
const googleDocsViewerUrl = computed(() => {
    if (isPDF.value || isPowerPoint.value) {
        return `https://docs.google.com/viewer?url=${encodeURIComponent(viewUrl.value)}&embedded=true`;
    }
    return null;
});

const embedUrl = computed(() => {
    if (isVideo.value) {
        return viewUrl.value;
    }
    
    if (isPowerPoint.value) {
        // Use Cloudinary transformation for PowerPoint preview (first slide)
        return `https://res.cloudinary.com/${cloudName}/image/upload/f_auto,q_auto,pg_1/${presentation.cloudinary_public_id}.jpg`;
    }
    
    return viewUrl.value;
});

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
        <!-- Header -->
        <div
            class="border-b border-sidebar-border/70 bg-muted/20 px-4 py-6 dark:border-sidebar-border sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-5xl">
                <div class="mb-4">
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/presentations">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Presentations
                        </Link>
                    </Button>
                </div>

                <h1
                    class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                >
                    {{ presentation.title }}
                </h1>

                <div
                    class="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
                >
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
                </div>

                <div v-if="isAdmin" class="mt-4 flex gap-2">
                    <Button as-child variant="outline" size="sm">
                        <Link
                            :href="`/admin/presentations/${presentation.id}/edit`"
                        >
                            <Edit class="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </Button>
                    <Button
                        variant="destructive"
                        size="sm"
                        @click="showDeleteDialog = true"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Description -->
            <div
                v-if="presentation.description"
                class="mb-8 rounded-lg border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
            >
                <h2 class="mb-2 text-lg font-semibold text-foreground">
                    Description
                </h2>
                <p class="whitespace-pre-wrap text-muted-foreground">
                    {{ presentation.description }}
                </p>
            </div>

            <!-- Viewer -->
            <div
                class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-card dark:border-sidebar-border"
            >
                <!-- PDF Viewer -->
                <div v-if="isPDF" class="bg-gray-100 dark:bg-gray-900">
                    <iframe
                        :src="googleDocsViewerUrl"
                        class="h-[800px] w-full"
                        frameborder="0"
                        loading="lazy"
                    >
                    </iframe>
                    <div class="border-t border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <p class="text-sm text-muted-foreground">
                                Viewing PDF via Google Docs Viewer
                            </p>
                            <div class="flex gap-2">
                                <Button as-child variant="outline" size="sm">
                                    <a
                                        :href="pdfViewerUrl"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <ExternalLink class="mr-2 h-4 w-4" />
                                        Open in New Tab
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Player -->
                <div v-else-if="isVideo" class="aspect-video bg-black">
                    <video
                        :src="embedUrl"
                        controls
                        class="h-full w-full"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- PowerPoint Preview -->
                <div v-else-if="isPowerPoint" class="p-6">
                    <div class="mb-6">
                        <iframe
                            :src="googleDocsViewerUrl"
                            class="h-[800px] w-full rounded border border-sidebar-border/70 dark:border-sidebar-border"
                            frameborder="0"
                            loading="lazy"
                        >
                        </iframe>
                    </div>
                    <div class="text-center">
                        <div class="mb-4 text-sm text-muted-foreground">
                            <p>Viewing PowerPoint presentation inline</p>
                            <p class="mt-1">Use the controls below to navigate or open in a new tab</p>
                        </div>
                        <div class="flex justify-center gap-2">
                            <Button as-child variant="outline">
                                <a
                                    :href="viewUrl"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <ExternalLink class="mr-2 h-4 w-4" />
                                    Open in New Tab
                                </a>
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Other File Types -->
                <div v-else class="p-6 text-center">
                    <p class="mb-4 text-muted-foreground">
                        Preview not available for this file type
                    </p>
                    <Button as-child>
                        <a
                            :href="viewUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            View File
                        </a>
                    </Button>
                </div>
            </div>

            <!-- File Info -->
            <div
                class="mt-6 rounded-lg border border-sidebar-border/70 bg-muted/10 p-4 dark:border-sidebar-border"
            >
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">
                            File Name
                        </dt>
                        <dd class="mt-1 text-sm text-foreground">
                            {{ presentation.filename }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-muted-foreground">
                            File Type
                        </dt>
                        <dd class="mt-1 text-sm text-foreground">
                            {{ presentation.file_type }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
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
                <Button
                    variant="outline"
                    @click="showDeleteDialog = false"
                    :disabled="isDeleting"
                >
                    Cancel
                </Button>
                <Button
                    variant="destructive"
                    @click="handleDelete"
                    :disabled="isDeleting"
                >
                    {{ isDeleting ? 'Deleting...' : 'Delete' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
