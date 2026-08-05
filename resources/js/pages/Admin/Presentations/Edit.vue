<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'vue-sonner';

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    filename: string;
    file_size: number;
    file_type: string;
    cloudinary_url: string;
    created_at: string;
}

interface Props {
    presentation: Presentation;
}

const { presentation } = defineProps<Props>();

const form = useForm({
    title: presentation.title,
    description: presentation.description || '',
});

const submit = () => {
    if (!form.title.trim()) {
        toast.error('Please provide a title');
        return;
    }

    form.put(`/admin/presentations/${presentation.id}`, {
        onSuccess: () => {
            toast.success('Presentation updated successfully!');
        },
        onError: (errors) => {
            console.error('Update errors:', errors);
            if (errors.title) {
                toast.error(errors.title);
            } else if (errors.description) {
                toast.error(errors.description);
            } else {
                toast.error('Failed to update presentation. Please try again.');
            }
        },
    });
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
</script>

<template>
    <Head :title="`Edit ${presentation.title}`" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <div
            class="border-b border-sidebar-border/70 bg-muted/20 px-4 py-6 dark:border-sidebar-border sm:px-6 lg:px-8"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-4">
                    <Button as-child variant="ghost" size="sm">
                        <Link :href="`/presentations/${presentation.id}`">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back to Presentation
                        </Link>
                    </Button>
                </div>

                <h1
                    class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl"
                >
                    Edit Presentation
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Update the title and description
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Current File Info -->
            <div
                class="mb-8 rounded-lg border border-sidebar-border/70 bg-muted/10 p-6 dark:border-sidebar-border"
            >
                <h2 class="mb-3 text-sm font-medium text-muted-foreground">
                    Current File
                </h2>
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-2xl"
                    >
                        {{
                            presentation.file_type.includes('video')
                                ? '🎥'
                                : '📊'
                        }}
                    </div>
                    <div>
                        <p class="font-medium text-foreground">
                            {{ presentation.filename }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ formatFileSize(presentation.file_size) }} •
                            {{ presentation.file_type }}
                        </p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-muted-foreground">
                    Note: You cannot change the file. To upload a different
                    file, create a new presentation.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
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
                    <div
                        class="mt-1 flex justify-between text-xs text-muted-foreground"
                    >
                        <span
                            v-if="form.errors.description"
                            class="text-destructive"
                        >
                            {{ form.errors.description }}
                        </span>
                        <span class="ml-auto">
                            {{ form.description.length }}/1000
                        </span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                    <Button
                        as-child
                        variant="outline"
                        :disabled="form.processing"
                    >
                        <Link :href="`/presentations/${presentation.id}`">
                            Cancel
                        </Link>
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
