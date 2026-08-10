<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Plus, Pencil, Trash2, GripVertical, FolderPlus, ChevronDown, ChevronRight } from '@lucide/vue';
import { ref } from 'vue';
import draggable from 'vuedraggable';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { toast } from 'vue-sonner';

defineOptions({ layout: AppLayout });

interface SubCategory {
    id: number;
    name: string;
    description: string | null;
    color: string;
    order: number;
    is_active: boolean;
    presentations_count: number;
}

interface Category extends SubCategory {
    children: SubCategory[];
}

interface Props {
    categories: Category[];
}

const props = defineProps<Props>();

// ── Local state ────────────────────────────────────────────────────────────
const showCreateDialog  = ref(false);
const showEditDialog    = ref(false);
const showDeleteDialog  = ref(false);
const categoryToDelete  = ref<Category | SubCategory | null>(null);
const categoryToEdit    = ref<Category | SubCategory | null>(null);
const expandedParents   = ref<Set<number>>(new Set(props.categories.map(c => c.id)));
const sortableCategories = ref<Category[]>([...props.categories]);

// ── Forms ──────────────────────────────────────────────────────────────────
const form = useForm({
    name:        '',
    description: '',
    color:       '#16a34a',
    is_active:   true,
    parent_id:   null as number | null,
});

const editForm = useForm({
    name:        '',
    description: '',
    color:       '#16a34a',
    is_active:   true,
    parent_id:   null as number | null,
});

// ── Helpers ────────────────────────────────────────────────────────────────
const toggleExpand = (id: number) => {
    if (expandedParents.value.has(id)) {
        expandedParents.value.delete(id);
    } else {
        expandedParents.value.add(id);
    }
};

const openCreateAsChild = (parent: Category) => {
    form.reset();
    form.color     = parent.color;
    form.parent_id = parent.id;
    showCreateDialog.value = true;
};

const openCreateParent = () => {
    form.reset();
    form.color     = '#16a34a';
    form.parent_id = null;
    showCreateDialog.value = true;
};

// ── CRUD ───────────────────────────────────────────────────────────────────
const createCategory = () => {
    form.post('/admin/categories', {
        onSuccess: () => {
            toast.success('Category created successfully!');
            showCreateDialog.value = false;
            form.reset();
        },
        onError: (errors) => {
            toast.error(errors.name ?? errors.parent_id ?? 'Failed to create category');
        },
    });
};

const openEditDialog = (category: Category | SubCategory) => {
    categoryToEdit.value    = category;
    editForm.name           = category.name;
    editForm.description    = category.description || '';
    editForm.color          = category.color;
    editForm.is_active      = category.is_active;
    editForm.parent_id      = (category as any).parent_id ?? null;
    showEditDialog.value    = true;
};

const updateCategory = () => {
    if (!categoryToEdit.value) return;
    editForm.put(`/admin/categories/${categoryToEdit.value.id}`, {
        onSuccess: () => {
            toast.success('Category updated!');
            showEditDialog.value = false;
        },
        onError: (errors) => {
            toast.error(errors.name ?? errors.parent_id ?? 'Failed to update');
        },
    });
};

const openDeleteDialog = (category: Category | SubCategory) => {
    categoryToDelete.value = category;
    showDeleteDialog.value = true;
};

const deleteCategory = () => {
    if (!categoryToDelete.value) return;
    router.delete(`/admin/categories/${categoryToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Category deleted!');
            showDeleteDialog.value = false;
            categoryToDelete.value = null;
        },
        onError: () => toast.error('Failed to delete category'),
    });
};

const onDragEnd = () => {
    const updates = sortableCategories.value.map((cat, idx) => ({ id: cat.id, order: idx }));
    router.post('/admin/categories/reorder', { categories: updates }, {
        preserveScroll: true,
        onSuccess: () => toast.success('Order updated!'),
        onError: () => {
            toast.error('Failed to update order');
            sortableCategories.value = [...props.categories];
        },
    });
};

const hasChildren = (cat: Category) => cat.children && cat.children.length > 0;
const totalCount  = (cat: Category) =>
    cat.presentations_count + cat.children.reduce((s, c) => s + c.presentations_count, 0);
</script>

<template>
    <Head title="Manage Categories" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Categories</h1>
                <p class="text-muted-foreground">
                    Manage parent categories and their sub-categories
                </p>
            </div>

            <!-- Create Category Dialog -->
            <Dialog v-model:open="showCreateDialog">
                <DialogTrigger as-child>
                    <Button @click="openCreateParent">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Category
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle>
                            {{ form.parent_id ? 'Add Sub-Category' : 'Add Category' }}
                        </DialogTitle>
                        <DialogDescription>
                            {{ form.parent_id
                                ? 'Create a sub-category under the selected parent.'
                                : 'Create a top-level category folder.' }}
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="createCategory" class="space-y-4">
                        <!-- Parent selector (only visible when adding top-level, can change to sub) -->
                        <div>
                            <Label>Parent Category</Label>
                            <Select v-model="form.parent_id" class="mt-2">
                                <SelectTrigger>
                                    <SelectValue placeholder="None (top-level category)" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="null">None — top-level folder</SelectItem>
                                    <SelectItem
                                        v-for="cat in sortableCategories"
                                        :key="cat.id"
                                        :value="cat.id"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: cat.color }" />
                                            {{ cat.name }}
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors.parent_id" class="mt-1 text-sm text-destructive">{{ form.errors.parent_id }}</p>
                        </div>

                        <div>
                            <Label for="name">Name *</Label>
                            <Input id="name" v-model="form.name" type="text" required placeholder="e.g., Komunikasyon" class="mt-2" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Brief description"
                                class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                rows="2"
                            />
                        </div>

                        <div>
                            <Label for="color">Color</Label>
                            <div class="flex gap-2 mt-2">
                                <Input id="color" v-model="form.color" type="color" class="w-20 h-10" />
                                <Input v-model="form.color" type="text" placeholder="#16a34a" class="flex-1" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <input id="is_active" v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
                            <Label for="is_active" class="cursor-pointer">Active</Label>
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" @click="showCreateDialog = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Empty state -->
        <div v-if="categories.length === 0" class="text-center py-12">
            <p class="text-muted-foreground text-lg">No categories yet.</p>
            <Button @click="openCreateParent" class="mt-4">
                <Plus class="mr-2 h-4 w-4" />
                Create your first category
            </Button>
        </div>

        <!-- Category tree (draggable at parent level) -->
        <draggable
            v-else
            v-model="sortableCategories"
            item-key="id"
            @end="onDragEnd"
            handle=".drag-handle"
            class="space-y-3"
        >
            <template #item="{ element: category }">
                <Card class="overflow-hidden">
                    <!-- Parent row -->
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-3">
                            <!-- Drag handle -->
                            <div class="drag-handle cursor-move flex-shrink-0">
                                <GripVertical class="h-5 w-5 text-muted-foreground" />
                            </div>

                            <!-- Expand toggle -->
                            <button
                                v-if="hasChildren(category)"
                                class="flex-shrink-0 text-muted-foreground hover:text-foreground transition-colors"
                                @click="toggleExpand(category.id)"
                            >
                                <ChevronDown v-if="expandedParents.has(category.id)" class="h-4 w-4" />
                                <ChevronRight v-else class="h-4 w-4" />
                            </button>
                            <div v-else class="w-4 flex-shrink-0" />

                            <!-- Color swatch + name -->
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <div class="w-4 h-4 rounded flex-shrink-0" :style="{ backgroundColor: category.color }" />
                                <div class="min-w-0">
                                    <CardTitle class="text-base">{{ category.name }}</CardTitle>
                                    <CardDescription v-if="category.description" class="truncate">{{ category.description }}</CardDescription>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Badge :variant="category.is_active ? 'default' : 'secondary'">
                                    {{ category.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                                <span class="text-xs text-muted-foreground whitespace-nowrap">
                                    {{ category.children.length }} sub · {{ totalCount(category) }} files
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <Button
                                    variant="ghost" size="icon"
                                    title="Add sub-category"
                                    @click="openCreateAsChild(category)"
                                >
                                    <FolderPlus class="h-4 w-4 text-green-600" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="openEditDialog(category)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon" @click="openDeleteDialog(category)">
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </div>
                    </CardHeader>

                    <!-- Sub-category list (collapsible) -->
                    <Transition name="slide">
                        <CardContent
                            v-if="hasChildren(category) && expandedParents.has(category.id)"
                            class="pt-0 pb-3"
                        >
                            <div class="ml-10 space-y-1 border-l-2 pl-4" :style="{ borderColor: category.color + '66' }">
                                <div
                                    v-for="child in category.children"
                                    :key="child.id"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-muted/50 transition-colors"
                                >
                                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ backgroundColor: child.color }" />
                                    <span class="flex-1 text-sm font-medium min-w-0 truncate">{{ child.name }}</span>
                                    <span class="text-xs text-muted-foreground flex-shrink-0">
                                        {{ child.presentations_count }} file{{ child.presentations_count !== 1 ? 's' : '' }}
                                    </span>
                                    <Badge :variant="child.is_active ? 'default' : 'secondary'" class="text-[10px] px-1.5 py-0">
                                        {{ child.is_active ? 'Active' : 'Off' }}
                                    </Badge>
                                    <Button variant="ghost" size="icon" class="h-7 w-7" @click="openEditDialog(child)">
                                        <Pencil class="h-3 w-3" />
                                    </Button>
                                    <Button variant="ghost" size="icon" class="h-7 w-7" @click="openDeleteDialog(child)">
                                        <Trash2 class="h-3 w-3 text-destructive" />
                                    </Button>
                                </div>

                                <!-- Add sub-category inline shortcut -->
                                <button
                                    class="flex items-center gap-2 px-3 py-1.5 text-xs text-muted-foreground hover:text-foreground hover:bg-muted/50 rounded-lg w-full transition-colors"
                                    @click="openCreateAsChild(category)"
                                >
                                    <Plus class="h-3 w-3" />
                                    Add sub-category
                                </button>
                            </div>
                        </CardContent>
                    </Transition>
                </Card>
            </template>
        </draggable>

        <!-- ── Edit Dialog ─────────────────────────────────────────────── -->
        <Dialog v-model:open="showEditDialog">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Edit Category</DialogTitle>
                    <DialogDescription>Update category information</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="updateCategory" class="space-y-4">
                    <div>
                        <Label>Parent Category</Label>
                        <Select v-model="editForm.parent_id" class="mt-2">
                            <SelectTrigger>
                                <SelectValue placeholder="None (top-level)" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="null">None — top-level folder</SelectItem>
                                <SelectItem
                                    v-for="cat in sortableCategories.filter(c => c.id !== categoryToEdit?.id)"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    <div class="flex items-center gap-2">
                                        <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: cat.color }" />
                                        {{ cat.name }}
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="editForm.errors.parent_id" class="mt-1 text-sm text-destructive">{{ editForm.errors.parent_id }}</p>
                    </div>

                    <div>
                        <Label for="edit-name">Name *</Label>
                        <Input id="edit-name" v-model="editForm.name" type="text" required class="mt-2" />
                        <p v-if="editForm.errors.name" class="mt-1 text-sm text-destructive">{{ editForm.errors.name }}</p>
                    </div>

                    <div>
                        <Label for="edit-description">Description</Label>
                        <textarea
                            id="edit-description"
                            v-model="editForm.description"
                            class="mt-2 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            rows="2"
                        />
                    </div>

                    <div>
                        <Label for="edit-color">Color</Label>
                        <div class="flex gap-2 mt-2">
                            <Input id="edit-color" v-model="editForm.color" type="color" class="w-20 h-10" />
                            <Input v-model="editForm.color" type="text" class="flex-1" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input id="edit-is_active" v-model="editForm.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
                        <Label for="edit-is_active" class="cursor-pointer">Active</Label>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showEditDialog = false">Cancel</Button>
                        <Button type="submit" :disabled="editForm.processing">
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- ── Delete Confirmation ─────────────────────────────────────── -->
        <Dialog v-model:open="showDeleteDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Category</DialogTitle>
                    <DialogDescription>
                        <span v-if="categoryToDelete">
                            Delete "<strong>{{ categoryToDelete.name }}</strong>"?
                            <span class="block mt-2 text-destructive text-sm">
                                This action cannot be undone. Categories with sub-categories or presentations cannot be deleted.
                            </span>
                        </span>
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
                    <Button variant="destructive" @click="deleteCategory">Delete</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.25s ease;
    overflow: hidden;
}
.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    max-height: 0;
}
.slide-enter-to,
.slide-leave-from {
    opacity: 1;
    max-height: 600px;
}
</style>
