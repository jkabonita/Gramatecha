<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { ArrowLeft, Upload, X } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

const form = useForm({
    file: null as File | null,
    title: '',
    description: '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const acceptedFileTypes = '.ppt,.pptx,.mp4,.mov';
const maxFileSize = 100 * 1024 * 1024; // 100MB

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

const validateAndSetFile = (file: File) => {
    // Check file size
    if (file.size > maxFileSize) {
        toast.error('File size must be less than 100MB');
        return;
    }

    // Check file type
    const extension = file.name.split('.').pop()?.toLowerCase();
    const allowedExtensions = ['ppt', 'pptx', 'mp4', 'mov'];
    
    if (!extension || !allowedExtensions.includes(extension)) {
        toast.error('Only .ppt, .pptx, .mp4, and .mov files are allowed');
        return;
    }

    form.file = file;

    // Auto-fill title if empty
    if (!form.title) {
        const nameWithoutExtension = file.name.replace(/\.[^/.]+$/, '');
        form.title = nameWithoutExtension;
    }
};

const removeFile = () => {
    form.file = null;
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
    if (!form.file) {
        toast.error('Please select a file to upload');
        return;
    }

    if (!form.title.trim()) {
        toast.error('Please provide a title');
        return;
    }

    form.post('/admin/presentations', {
        onSuccess: () => {
            toast.success('Presentation uploaded successfully!');
        },
        onError: (errors) => {
            console.error('Upload errors:', errors);
            if (errors.file) {
                toast.error(errors.file);
            } else if (errors.title) {
                toast.error(errors.title);
            } else {
                toast.error('Failed to upload presentation. Please try again.');
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
                    Upload New Presentation
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Upload a PowerPoint presentation or video file
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
                                PPT, PPTX, MP4, MOV up to 100MB
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

                <!-- Upload Progress -->
                <div
                    v-if="form.processing"
                    class="rounded-lg border border-sidebar-border/70 bg-muted/10 p-4 dark:border-sidebar-border"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="h-5 w-5 animate-spin rounded-full border-2 border-primary border-t-transparent"
                        />
                        <p class="text-sm text-muted-foreground">
                            Uploading presentation... Please wait.
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3">
                    <Button
                        type="submit"
                        :disabled="form.processing || !form.file"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Uploading...' : 'Upload' }}
                    </Button>
                    <Button
                        as-child
                        variant="outline"
                        :disabled="form.processing"
                    >
                        <Link href="/presentations"> Cancel </Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
