<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { ArrowLeft, Upload, X, FolderOpen } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Progress } from '@/components/ui/progress';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { compressFiles } from '@/utils/fileCompression';

defineOptions({
    layout: AppLayout,
});

interface SubCategory {
    id: number;
    name: string;
    color: string;
    selectable: boolean;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    color: string;
    selectable: boolean;
    children: SubCategory[];
}

interface FilePreview {
    file: File;
    name: string;
    size: string;
    type: string;
}

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    files: [] as File[],
    category_id: null as number | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const uploadProgress = ref(0);
const selectedCategoryId = ref<number | null>(null);
const isCompressing = ref(false);
const compressionProgress = ref(0);
const currentCompressingFile = ref<string>('');

const acceptedFileTypes = '.pdf,.ppt,.pptx,.mp4,.mov';
const maxFileSize = 500 * 1024 * 1024; // 500MB
const maxFiles = 20;

const filePreviews = computed<FilePreview[]>(() => {
    return form.files.map(file => {
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        return {
            file,
            name: file.name,
            size: `${sizeInMB} MB`,
            type: file.type,
        };
    });
});

const totalSize = computed(() => {
    const total = form.files.reduce((sum, file) => sum + file.size, 0);
    return (total / (1024 * 1024)).toFixed(2);
});

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        addFiles(Array.from(target.files));
    }
};

const handleDrop = (event: DragEvent) => {
    isDragging.value = false;
    event.preventDefault();

    if (event.dataTransfer?.files) {
        addFiles(Array.from(event.dataTransfer.files));
    }
};

const addFiles = (newFiles: File[]) => {
    const validFiles: File[] = [];
    
    for (const file of newFiles) {
        // Check if we've reached max files
        if (form.files.length + validFiles.length >= maxFiles) {
            toast.error(`You can only upload up to ${maxFiles} files at once`);
            break;
        }

        // Check if file already exists
        if (form.files.some(f => f.name === file.name && f.size === file.size)) {
            toast.error(`File "${file.name}" is already added`);
            continue;
        }

        // Check file size
        if (file.size > maxFileSize) {
            toast.error(`File "${file.name}" is too large (max 500MB)`);
            continue;
        }

        // Check file type
        const extension = file.name.split('.').pop()?.toLowerCase();
        const allowedExtensions = ['pdf', 'ppt', 'pptx', 'mp4', 'mov'];
        
        if (!extension || !allowedExtensions.includes(extension)) {
            toast.error(`File "${file.name}" has an invalid type`);
            continue;
        }

        validFiles.push(file);
    }

    if (validFiles.length > 0) {
        form.files = [...form.files, ...validFiles];
        toast.success(`Added ${validFiles.length} file(s)`);
    }
};

const removeFile = (index: number) => {
    form.files = form.files.filter((_, i) => i !== index);
    
    // Clear the file input
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const removeAllFiles = () => {
    form.files = [];
    if (fileInput.value) {
        fileInput.value.value = '';
    }
    toast.success('All files removed');
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const submit = async () => {
    // Validate files
    if (form.files.length === 0) {
        toast.error('Please select at least one file to upload');
        return;
    }

    // Set form category_id (can be null for "Uncategorized")
    form.category_id = selectedCategoryId.value;

    // Compress video files before upload
    const hasVideos = form.files.some(f => {
        const ext = f.name.split('.').pop()?.toLowerCase();
        return ext && ['mp4', 'mov', 'avi', 'webm'].includes(ext);
    });

    let filesToUpload = form.files;

    if (hasVideos) {
        isCompressing.value = true;
        compressionProgress.value = 0;
        
        try {
            filesToUpload = await compressFiles(form.files, {
                onFileProgress: (fileName, progress) => {
                    currentCompressingFile.value = fileName;
                    compressionProgress.value = progress;
                },
                onOverallProgress: (current, total) => {
                    // Update overall progress in toast
                },
            });
            
            isCompressing.value = false;
            toast.success('Videos compressed! Starting upload...');
        } catch (error) {
            isCompressing.value = false;
            toast.warning('Compression skipped, uploading original files');
        }
    }

    // Update form with compressed files
    form.files = filesToUpload;
    uploadProgress.value = 0;

    form.post('/admin/presentations/bulk-store', {
        onProgress: (progress) => {
            if (progress?.percentage) {
                uploadProgress.value = progress.percentage;
            }
        },
        onSuccess: () => {
            toast.success(`${form.files.length} presentations uploaded successfully!`);
            uploadProgress.value = 100;
        },
        onError: (errors) => {
            uploadProgress.value = 0;
            
            if (errors.files) {
                toast.error(Array.isArray(errors.files) ? errors.files[0] : errors.files);
            } else if (errors.category_id) {
                toast.error(errors.category_id);
            } else {
                const firstError = Object.values(errors)[0];
                toast.error(typeof firstError === 'string' ? firstError : 'Failed to upload presentations. Please try again.');
            }
        },
        onFinish: () => {
            if (!form.hasErrors && form.wasSuccessful) {
                uploadProgress.value = 100;
            }
        },
    });
};
</script>

<template>
    <Head title="Bulk Upload Presentations" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <div
            class="border-b border-sidebar-border/70 bg-muted/20 px-4 py-6 dark:border-sidebar-border sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-4xl">
                <div class="mb-4">
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/admin/presentations">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Presentations
                        </Link>
                    </Button>
                </div>

                <div class="flex items-start justify-between">
                    <div>
                        <h1
                            class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                        >
                            Bulk Upload Presentations
                        </h1>
                        <p class="mt-2 text-muted-foreground">
                            Upload multiple presentations to a specific category at once
                        </p>
                    </div>
                    <Badge variant="secondary" class="text-sm">
                        Max {{ maxFiles }} files
                    </Badge>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Category Selection (Optional) -->
                <div>
                    <Label for="category" class="text-base font-semibold">
                        Category (Optional)
                    </Label>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Select a category or leave blank for "Uncategorized"
                    </p>
                    <select
                        v-model="selectedCategoryId"
                        :disabled="form.processing"
                        class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option :value="null">— Uncategorized —</option>
                        <template v-for="cat in categories" :key="cat.id">
                            <!-- Parent has no children: selectable directly -->
                            <option v-if="cat.children.length === 0" :value="cat.id">
                                📁 {{ cat.name }}
                            </option>
                            <!-- Parent has children: show as disabled group header + children -->
                            <template v-else>
                                <option disabled>📁 {{ cat.name }}</option>
                                <option
                                    v-for="child in cat.children"
                                    :key="child.id"
                                    :value="child.id"
                                >
                                    &nbsp;&nbsp;&nbsp;&nbsp;└ {{ child.name }}
                                </option>
                            </template>
                        </template>
                    </select>
                    <p
                        v-if="form.errors.category_id"
                        class="mt-2 text-sm text-destructive"
                    >
                        {{ form.errors.category_id }}
                    </p>
                </div>

                <!-- File Upload Area -->
                <div>
                    <Label class="text-base font-semibold">Files *</Label>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Select up to {{ maxFiles }} files (PDF, PPT, PPTX, MP4, MOV). Videos will be compressed before upload for faster transfer.
                    </p>
                    
                    <!-- Drop Zone -->
                    <div
                        class="mt-3 rounded-lg border-2 border-dashed transition-colors"
                        :class="[
                            isDragging
                                ? 'border-primary bg-primary/5'
                                : 'border-sidebar-border/70 dark:border-sidebar-border',
                            form.errors.files
                                ? 'border-destructive'
                                : '',
                        ]"
                        @dragover="handleDragOver"
                        @dragleave="handleDragLeave"
                        @drop="handleDrop"
                    >
                        <div class="p-12 text-center">
                            <FolderOpen
                                class="mx-auto h-12 w-12 text-muted-foreground"
                            />
                            <div class="mt-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="fileInput?.click()"
                                    :disabled="form.processing || form.files.length >= maxFiles"
                                >
                                    <Upload class="mr-2 h-4 w-4" />
                                    Choose Files
                                </Button>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    :accept="acceptedFileTypes"
                                    @change="handleFileSelect"
                                    multiple
                                    class="hidden"
                                />
                            </div>
                            <p class="mt-2 text-sm text-muted-foreground">
                                or drag and drop multiple files here
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                PDF, PPT, PPTX, MP4, MOV up to 500MB each
                            </p>
                        </div>
                    </div>
                    <p
                        v-if="form.errors.files"
                        class="mt-2 text-sm text-destructive"
                    >
                        {{ form.errors.files }}
                    </p>
                </div>

                <!-- File List -->
                <div v-if="filePreviews.length > 0" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <Label class="text-base font-semibold">
                            Selected Files ({{ filePreviews.length }})
                        </Label>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-muted-foreground">
                                Total: {{ totalSize }} MB
                            </span>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="removeAllFiles"
                                :disabled="form.processing"
                            >
                                Clear All
                            </Button>
                        </div>
                    </div>

                    <div
                        class="max-h-96 space-y-2 overflow-y-auto rounded-lg border border-sidebar-border/70 bg-muted/10 p-4 dark:border-sidebar-border"
                    >
                        <div
                            v-for="(preview, index) in filePreviews"
                            :key="index"
                            class="flex items-center justify-between rounded-md border border-sidebar-border/50 bg-background p-3 dark:border-sidebar-border/50"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-primary/10 text-lg"
                                >
                                    {{
                                        preview.type.includes('video')
                                            ? '🎥'
                                            : '📊'
                                    }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="truncate font-medium text-foreground"
                                        :title="preview.name"
                                    >
                                        {{ preview.name }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ preview.size }}
                                    </p>
                                </div>
                            </div>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                @click="removeFile(index)"
                                :disabled="form.processing"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Compression Progress -->
                <div
                    v-if="isCompressing"
                    class="rounded-lg border border-blue-500/50 bg-blue-50/50 p-6 dark:border-blue-500/30 dark:bg-blue-950/20"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-5 w-5 animate-spin rounded-full border-2 border-blue-500 border-t-transparent"
                                />
                                <div>
                                    <p class="text-sm font-medium text-foreground">
                                        Compressing videos before upload...
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ currentCompressingFile }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                {{ Math.round(compressionProgress) }}%
                            </span>
                        </div>
                        <Progress :model-value="compressionProgress" class="h-2" />
                        <p class="text-xs text-muted-foreground">
                            ✨ Compressing in your browser to make uploads much faster!
                        </p>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div
                    v-if="form.processing"
                    class="rounded-lg border border-sidebar-border/70 bg-muted/10 p-6 dark:border-sidebar-border"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-5 w-5 animate-spin rounded-full border-2 border-primary border-t-transparent"
                                />
                                <p class="text-sm font-medium text-foreground">
                                    Uploading {{ filePreviews.length }} file{{ filePreviews.length !== 1 ? 's' : '' }}...
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-primary">
                                {{ Math.round(uploadProgress) }}%
                            </span>
                        </div>
                        <Progress :model-value="uploadProgress" class="h-2" />
                        <p class="text-xs text-muted-foreground">
                            Please wait while we process your files...
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3">
                    <Button
                        type="submit"
                        :disabled="form.processing || isCompressing || filePreviews.length === 0"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Uploading...' : isCompressing ? 'Compressing...' : `Upload ${filePreviews.length} File${filePreviews.length !== 1 ? 's' : ''}` }}
                    </Button>
                    
                    <Button
                        as-child
                        variant="outline"
                        :disabled="form.processing || isCompressing"
                    >
                        <Link href="/admin/presentations"> Cancel </Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
