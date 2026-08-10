<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Pencil, Trash2, Eye, Upload, CheckSquare, Square, SquareX } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { toast } from 'vue-sonner';

defineOptions({ layout: AppLayout });

interface Category { id: number; name: string; }

interface Presentation {
    id: number;
    title: string;
    description: string | null;
    file_type: string;
    file_size: number;
    views: number;
    category?: Category;
    created_at: string;
}

interface Props {
    presentations: {
        data: Presentation[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    categories: Category[];
}

const props = defineProps<Props>();

// ── Selection state ────────────────────────────────────────────────────────
const selectedIds      = ref<Set<number>>(new Set());
const showBulkDialog   = ref(false);
const isBulkDeleting   = ref(false);

const allIds = computed(() => props.presentations.data.map(p => p.id));
const isAllSelected = computed(
    () => allIds.value.length > 0 && allIds.value.every(id => selectedIds.value.has(id))
);
const isIndeterminate = computed(
    () => allIds.value.some(id => selectedIds.value.has(id)) && !isAllSelected.value
);
const selectionCount = computed(() => selectedIds.value.size);

const toggleAll = () => {
    if (isAllSelected.value) {
        selectedIds.value = new Set();
    } else {
        selectedIds.value = new Set(allIds.value);
    }
};

const toggleOne = (id: number) => {
    const next = new Set(selectedIds.value);
    next.has(id) ? next.delete(id) : next.add(id);
    selectedIds.value = next;
};

const clearSelection = () => { selectedIds.value = new Set(); };

// ── Single delete ──────────────────────────────────────────────────────────
const deletePresentation = (id: number) => {
    router.delete(`/admin/presentations/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            selectedIds.value.delete(id);
            toast.success('Presentation deleted.');
        },
    });
};

// ── Bulk delete ────────────────────────────────────────────────────────────
const confirmBulkDelete = () => { showBulkDialog.value = true; };

const executeBulkDelete = () => {
    isBulkDeleting.value = true;
    router.delete('/admin/presentations-bulk', {
        data: { ids: [...selectedIds.value] },
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`${selectionCount.value} presentation(s) deleted.`);
            selectedIds.value = new Set();
            showBulkDialog.value = false;
            isBulkDeleting.value = false;
        },
        onError: () => {
            toast.error('Bulk delete failed. Please try again.');
            isBulkDeleting.value = false;
        },
    });
};

// ── Helpers ────────────────────────────────────────────────────────────────
const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
};

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
</script>

<template>
    <Head title="Manage Presentations" />

    <div class="space-y-4 sm:space-y-6 p-3 sm:p-4 md:p-6">

        <!-- Header -->
        <div class="flex flex-col gap-3 sm:gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight">Presentations</h1>
                <p class="text-xs sm:text-sm md:text-base text-muted-foreground mt-1">
                    Manage and organize your presentation files
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <Button as-child variant="outline" size="sm" class="w-full sm:w-auto">
                    <Link href="/admin/presentations/bulk-create">
                        <Upload class="mr-2 h-4 w-4" />
                        <span>Bulk Upload</span>
                    </Link>
                </Button>
                <Button as-child size="sm" class="w-full sm:w-auto">
                    <Link href="/admin/presentations/create">
                        <Plus class="mr-2 h-4 w-4" />
                        <span>Upload Presentation</span>
                    </Link>
                </Button>
            </div>
        </div>

        <!-- ── Bulk action bar (shown when anything is selected) ──────── -->
        <Transition name="slide-down">
            <div
                v-if="selectionCount > 0"
                class="flex items-center justify-between rounded-lg border border-destructive/30 bg-destructive/5 px-4 py-3"
            >
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-destructive">
                        {{ selectionCount }} selected
                    </span>
                    <button
                        class="text-xs text-muted-foreground hover:text-foreground underline"
                        @click="clearSelection"
                    >
                        Clear
                    </button>
                </div>
                <Button
                    variant="destructive"
                    size="sm"
                    @click="confirmBulkDelete"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    Delete {{ selectionCount }} selected
                </Button>
            </div>
        </Transition>

        <!-- Empty state -->
        <div v-if="presentations.data.length === 0" class="text-center py-12">
            <p class="text-muted-foreground text-lg">No presentations found.</p>
            <Button as-child class="mt-4">
                <Link href="/admin/presentations/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Upload your first presentation
                </Link>
            </Button>
        </div>

        <!-- List -->
        <div v-else class="space-y-1">

            <!-- Select-all header row -->
            <div class="flex items-center gap-3 px-4 py-2 rounded-lg border border-transparent hover:bg-muted/30 transition-colors">
                <button
                    class="flex-shrink-0 text-muted-foreground hover:text-foreground transition-colors"
                    :title="isAllSelected ? 'Deselect all' : 'Select all'"
                    @click="toggleAll"
                >
                    <CheckSquare v-if="isAllSelected" class="h-5 w-5 text-destructive" />
                    <SquareX v-else-if="isIndeterminate" class="h-5 w-5 text-destructive/70" />
                    <Square v-else class="h-5 w-5" />
                </button>
                <span class="text-xs font-medium text-muted-foreground select-none">
                    {{ isAllSelected ? 'Deselect all' : `Select all (${allIds.length})` }}
                </span>
            </div>

            <!-- Presentation cards -->
            <Card
                v-for="presentation in presentations.data"
                :key="presentation.id"
                class="transition-all duration-150"
                :class="selectedIds.has(presentation.id) ? 'border-destructive/40 bg-destructive/5' : ''"
            >
                <CardHeader class="p-4 md:p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">

                        <!-- Checkbox + info -->
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <button
                                class="flex-shrink-0 mt-0.5 text-muted-foreground hover:text-destructive transition-colors"
                                @click="toggleOne(presentation.id)"
                            >
                                <CheckSquare
                                    v-if="selectedIds.has(presentation.id)"
                                    class="h-5 w-5 text-destructive"
                                />
                                <Square v-else class="h-5 w-5" />
                            </button>

                            <div class="flex-1 min-w-0">
                                <CardTitle class="text-base md:text-lg break-words leading-snug">
                                    {{ presentation.title }}
                                </CardTitle>
                                <CardDescription v-if="presentation.description" class="mt-0.5 break-words text-xs">
                                    {{ presentation.description }}
                                </CardDescription>
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    <Badge v-if="presentation.category" variant="secondary" class="text-xs">
                                        {{ presentation.category.name }}
                                    </Badge>
                                    <Badge variant="outline" class="uppercase text-xs">
                                        {{ presentation.file_type }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        {{ formatFileSize(presentation.file_size) }}
                                    </span>
                                    <span class="text-xs text-muted-foreground">
                                        · {{ presentation.views }} views
                                    </span>
                                    <span class="text-xs text-muted-foreground hidden sm:inline">
                                        · {{ formatDate(presentation.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 self-end md:self-start flex-shrink-0">
                            <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                                <Link :href="`/admin/presentations/${presentation.id}`">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="ghost" size="icon" class="h-8 w-8" as-child>
                                <Link :href="`/admin/presentations/${presentation.id}/edit`">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <!-- Single delete dialog -->
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="ghost" size="icon" class="h-8 w-8">
                                        <Trash2 class="h-4 w-4 text-destructive" />
                                    </Button>
                                </DialogTrigger>
                                <DialogContent class="max-w-[90vw] sm:max-w-md">
                                    <DialogHeader>
                                        <DialogTitle>Delete Presentation</DialogTitle>
                                        <DialogDescription class="break-words">
                                            Are you sure you want to delete "{{ presentation.title }}"?
                                            This action cannot be undone.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <DialogFooter class="gap-2 sm:gap-0">
                                        <Button variant="outline" class="w-full sm:w-auto">Cancel</Button>
                                        <Button
                                            variant="destructive"
                                            class="w-full sm:w-auto"
                                            @click="deletePresentation(presentation.id)"
                                        >
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                        </div>
                    </div>
                </CardHeader>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="presentations.last_page > 1" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs md:text-sm text-muted-foreground text-center sm:text-left">
                Showing {{ (presentations.current_page - 1) * presentations.per_page + 1 }} to
                {{ Math.min(presentations.current_page * presentations.per_page, presentations.total) }} of
                {{ presentations.total }} results
            </p>
            <div class="flex gap-2 justify-center">
                <Button variant="outline" size="sm" :disabled="presentations.current_page === 1" as-child class="flex-1 sm:flex-none">
                    <Link :href="`/admin/presentations?page=${presentations.current_page - 1}`">Previous</Link>
                </Button>
                <Button variant="outline" size="sm" :disabled="presentations.current_page === presentations.last_page" as-child class="flex-1 sm:flex-none">
                    <Link :href="`/admin/presentations?page=${presentations.current_page + 1}`">Next</Link>
                </Button>
            </div>
        </div>
    </div>

    <!-- ── Bulk Delete Confirmation Dialog ─────────────────────────────── -->
    <Dialog v-model:open="showBulkDialog">
        <DialogContent class="max-w-[90vw] sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Delete {{ selectionCount }} Presentation{{ selectionCount !== 1 ? 's' : '' }}</DialogTitle>
                <DialogDescription>
                    You are about to permanently delete <strong>{{ selectionCount }}</strong> presentation{{ selectionCount !== 1 ? 's' : '' }}.
                    This action <strong>cannot be undone</strong> and will remove all associated files.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:gap-0">
                <Button variant="outline" class="w-full sm:w-auto" @click="showBulkDialog = false">
                    Cancel
                </Button>
                <Button
                    variant="destructive"
                    class="w-full sm:w-auto"
                    :disabled="isBulkDeleting"
                    @click="executeBulkDelete"
                >
                    <Trash2 class="mr-2 h-4 w-4" />
                    {{ isBulkDeleting ? 'Deleting…' : `Delete ${selectionCount}` }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.2s ease;
    overflow: hidden;
}
.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-6px);
}
.slide-down-enter-to,
.slide-down-leave-from {
    opacity: 1;
    max-height: 80px;
    transform: translateY(0);
}
</style>
