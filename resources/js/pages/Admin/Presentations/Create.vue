<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { ArrowLeft, Upload, X } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { toast } from 'vue-sonner';
import { compressFile } from '@/utils/fileCompression';

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

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    file: null as File | null,
    title: '',
    description: '',
    category_id: null as number | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const uploadProgress = ref(0);
const compressionProgress = ref(0);
const isCompressing = ref(false);

const acceptedFileTypes = '.pdf,.ppt,.pptx,.mp4,.mov';
const maxFileSize = 500 * 1024 * 1024; // 500MB

const filePreview = computed(() => {
    if (!form.file) return null;

    const sizeInMB = (form.file.size / (1024 * 1024)).toFixed(2);
    return {
        name: form.file.name,
        size: `${sizeInMB} MB`,
        type: form.file.type,
    };
});

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        validateAndSetFile(target.files[0]);
    }
};

const handleDrop = (event: DragEvent) => {
    isDragging.value = false;
    event.preventDefault();

    if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
        validateAndSetFile(event.dataTransfer.files[0]);
    }
};

const validateAndSetFile = async (file: File) => {
    form.clearErrors('file');
    
    if (file.size > maxFileSize) {
        toast.error('File size must be less than 500MB');
        return;
    }

    const extension = file.name.split('.').pop()?.toLowerCase();
    const allowedExtensions = ['pdf', 'ppt', 'pptx', 'mp4', 'mov'];
    
    if (!extension || !allowedExtensions.includes(extension)) {
        toast.error('Only PDF, PPT, PPTX, MP4, and MOV files are allowed');
        return;
    }

    // Compress video files before setting
    if (['mp4', 'mov', 'avi', 'webm'].includes(extension)) {
        isCompressing.value = true;
        compressionProgress.value = 0;
        
        try {
            const compressed = await compressFile(file, {
                onProgress: (progress) => {
                    compressionProgress.value = progress;
                },
            });
            
            form.file = compressed;
            isCompressing.value = false;
            
            const reduction = ((1 - compressed.size / file.size) * 100).toFixed(1);
            if (compressed.size < file.size) {
                toast.success(`Video compressed: ${reduction}% smaller, faster upload!`);
            }
        } catch (error) {
            isCompressing.value = false;
            form.file = file; // Use original on error
        }
    } else {
        form.file = file;
    }

    if (!form.title) {
        const nameWithoutExtension = file.name.replace(/\.[^/.]+$/, '');
        form.title = nameWithoutExtension;
    }
};

const removeFile = () => {
    form.file = null;
    uploadProgress.value = 0;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragging.value = true;
};

const handleDragLeave = () => {
    isDragging.value = false;
};

const submit = () => {
    form.clearErrors();
    
    if (!form.file) {
        toast.error('Please select a file to upload');
        return;
    }

    if (!form.title.trim()) {
        toast.error('Please provide a title');
        return;
    }

    uploadProgress.value = 0;

    form.post('/admin/presentations', {
        onProgress: (progress) => {
            if (progress?.percentage) {
                uploadProgress.value = progress.percentage;
            }
        },
        onSuccess: () => {
            toast.success('Presentation uploaded successfully!');
            uploadProgress.value = 100;
        },
        onError: (errors) => {
            uploadProgress.value = 0;
            
            if (errors.file) {
                toast.error(errors.file);
            } else if (errors.title) {
                toast.error(errors.title);
            } else if (errors.category_id) {
                toast.error(errors.category_id);
            } else {
                const firstError = Object.values(errors)[0];
                toast.error(typeof firstError === 'string' ? firstError : 'Failed to upload presentation. Please try again.');
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
    <Head title="Upload Presentation" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <div
            class="border-b border-sidebar-border/70 bg-muted/20 px-4 py-6 dark:border-sidebar-border sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-4 flex items-center justify-between">
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/admin/presentations">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Presentations
                        </Link>
                    </Button>
                    <Button as-child variant="outline" size="sm">
                        <Link href="/admin/presentations/bulk-create">
                            <Upload class="mr-2 h-4 w-4" />
                            Bulk Upload
                        </Link>
                    </Button>
                </div>

                <h1
                    class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                >
                    Upload New Presentation
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Upload a PDF, PowerPoint presentation or video file
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- File Upload Area -->
                <div>
                    <Label>File *</Label>
                    <div
                        class="mt-2 rounded-lg border-2 border-dashed transition-colors"
                        :class="[
                            isDragging
                                ? 'border-primary bg-primary/5'
                                : 'border-sidebar-border/70 dark:border-sidebar-border',
                            form.errors.file
                                ? 'border-destructive'
                                : '',
                        ]"
                        @dragover="handleDragOver"
                        @dragleave="handleDragLeave"
                        @drop="handleDrop"
                    >
                        <!-- File Preview -->
                        <div
                            v-if="filePreview"
                            class="flex items-center justify-between p-6"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-2xl"
                                >
                                    {{
                                        filePreview.type.includes('video')
                                            ? '🎥'
                                            : '📊'
                                    }}
                                </div>
                                <div>
                                    <p
                                        class="font-medium text-foreground"
                                    >
                                        {{ filePreview.name }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ filePreview.size }}
                                    </p>
                                </div>
                            </div>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                @click="removeFile"
                                :disabled="form.processing"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>

                        <!-- Upload Prompt -->
                        <div v-else class="p-12 text-center">
                            <Upload
                                class="mx-auto h-12 w-12 text-muted-foreground"
                            />
                            <div class="mt-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="fileInput?.click()"
                                >
                                    Choose File
                                </Button>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    :accept="acceptedFileTypes"
                                    @change="handleFileSelect"
                                    class="hidden"
                                />
                            </div>
                            <p class="mt-2 text-sm text-muted-foreground">
                                or drag and drop
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">
                                PDF, PPT, PPTX, MP4, MOV up to 500MB
                            </p>
                            <p class="mt-1 text-xs text-blue-500">
                                ✨ Videos are compressed in your browser for faster upload
                            </p>
                        </div>
                    </div>
                    <p
                        v-if="form.errors.file"
                        class="mt-2 text-sm text-destructive"
                    >
                        {{ form.errors.file }}
                    </p>
                </div>

                <!-- Title Input -->
                <div>
                    <Label for="title">Title *</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Enter presentation title"
                        class="mt-2"
                        :disabled="form.processing"
                    />
                    <p
                        v-if="form.errors.title"
                        class="mt-2 text-sm text-destructive"
                    >
                        {{ form.errors.title }}
                    </p>
                </div>

                <!-- Description Textarea -->
                <div>
                    <Label for="description">Description (Optional)</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        maxlength="1000"
                        placeholder="Enter a brief description of the presentation"
                        class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="form.processing"
                    />
                    <div class="mt-1 flex justify-between text-xs text-muted-foreground">
                        <span v-if="form.errors.description" class="text-destructive">
                            {{ form.errors.description }}
                        </span>
                        <span class="ml-auto">
                            {{ form.description.length }}/1000
                        </span>
                    </div>
                </div>

                <!-- Category Selection -->
                <div>
                    <Label for="category">Category (Optional)</Label>
                    <select
                        v-model="form.category_id"
                        class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option :value="null">— No Category —</option>
                        <template v-for="cat in categories" :key="cat.id">
                            <option v-if="cat.children.length === 0" :value="cat.id">
                                📁 {{ cat.name }}
                            </option>
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

                <!-- Compression Progress -->
                <div
                    v-if="isCompressing"
                    class="rounded-lg border border-blue-500/50 bg-blue-50/50 p-4 dark:border-blue-500/30 dark:bg-blue-950/20"
                >
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="h-4 w-4 animate-spin rounded-full border-2 border-blue-500 border-t-transparent" />
                                <p class="text-sm font-medium text-foreground">
                                    Compressing video before upload...
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                {{ Math.round(compressionProgress) }}%
                            </span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-blue-200 dark:bg-blue-900">
                            <div
                                class="h-full bg-blue-500 transition-all duration-300 ease-out"
                                :style="{ width: `${compressionProgress}%` }"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            ✨ Compressing in your browser to make upload faster!
                        </p>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div
                    v-if="form.processing"
                    class="rounded-lg border border-sidebar-border/70 bg-muted/10 p-4 dark:border-sidebar-border"
                >
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-foreground">
                                Uploading…
                            </p>
                            <span class="text-sm font-semibold text-primary">
                                {{ uploadProgress }}%
                            </span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-secondary">
                            <div
                                class="h-full bg-primary transition-all duration-300 ease-out"
                                :style="{ width: `${uploadProgress}%` }"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Please wait while we process your file...
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3">
                    <Button
                        type="submit"
                        :disabled="form.processing || isCompressing || !form.file"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Uploading...' : isCompressing ? 'Compressing...' : 'Upload' }}
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
