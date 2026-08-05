<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Calendar, User, Eye } from '@lucide/vue';
import { ref, onMounted } from 'vue';

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

interface Props {
    presentation: Presentation;
    fileTypeIcon: string;
    formattedFileSize: string;
    index?: number;
}

const { presentation, fileTypeIcon, formattedFileSize, index = 0 } = defineProps<Props>();

const isVisible = ref(false);

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true;
    }, index * 100); // Stagger animation based on index
});

const truncateText = (text: string | null, maxLength: number): string => {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const createPlaceholderSvg = (icon: string): string => {
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="400" height="300">
            <rect width="400" height="300" fill="#1a1a1a"/>
            <text x="50%" y="50%" font-size="72" text-anchor="middle" dominant-baseline="middle" fill="#ffffff">
                ${icon}
            </text>
        </svg>
    `;
    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
};

const getThumbnailUrl = (): string => {
    const cloudName = 'io7qeps0'; // From config
    
    if (presentation.file_type.includes('video')) {
        // Video thumbnail from Cloudinary
        return `https://res.cloudinary.com/${cloudName}/video/upload/so_0,w_400,h_300,c_fill,q_auto/${presentation.cloudinary_public_id}.jpg`;
    }
    
    if (
        presentation.file_type.includes('presentation') ||
        presentation.file_type.includes('powerpoint')
    ) {
        // PowerPoint first page preview
        return `https://res.cloudinary.com/${cloudName}/image/upload/f_auto,q_auto,w_400,h_300,c_fill,pg_1/${presentation.cloudinary_public_id}.jpg`;
    }
    
    // Fallback - generic document icon
    return createPlaceholderSvg(fileTypeIcon);
};
</script>

<template>
    <Link
        :href="`/presentations/${presentation.id}`"
        :class="[
            'group block overflow-hidden rounded-xl border bg-card shadow-sm transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/10',
            'border-sidebar-border/70 dark:border-sidebar-border',
            isVisible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'
        ]"
    >
        <!-- Thumbnail -->
        <div class="relative aspect-video overflow-hidden bg-muted">
            <img
                :src="getThumbnailUrl()"
                :alt="presentation.title"
                class="h-full w-full object-cover transition-all duration-500 group-hover:scale-110 group-hover:brightness-75"
                loading="lazy"
                @error="
                    ($event.target as HTMLImageElement).src = createPlaceholderSvg(fileTypeIcon)
                "
            />
            <!-- Gradient overlay on hover -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
            
            <!-- File type badge -->
            <div class="absolute left-3 top-3 rounded-full bg-background/95 px-3 py-1.5 text-lg backdrop-blur-sm shadow-lg ring-1 ring-black/5 transition-transform duration-300 group-hover:scale-110">
                {{ fileTypeIcon }}
            </div>

            <!-- View indicator on hover -->
            <div class="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-background/95 px-3 py-1.5 text-xs font-medium backdrop-blur-sm opacity-0 transition-all duration-300 group-hover:opacity-100">
                <Eye class="h-3 w-3" />
                <span>View</span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <h3 class="mb-2 text-lg font-semibold text-foreground transition-colors line-clamp-2 group-hover:text-primary">
                {{ truncateText(presentation.title, 60) }}
            </h3>

            <p
                v-if="presentation.description"
                class="mb-4 line-clamp-2 text-sm text-muted-foreground"
            >
                {{ presentation.description }}
            </p>
            <p v-else class="mb-4 text-sm italic text-muted-foreground">
                No description available
            </p>

            <!-- Metadata -->
            <div class="space-y-2 text-xs text-muted-foreground">
                <div class="flex items-center gap-2 transition-colors group-hover:text-foreground">
                    <User class="h-3.5 w-3.5" />
                    <span class="font-medium">{{ presentation.uploader.name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Calendar class="h-3.5 w-3.5" />
                        <span>{{ formatDate(presentation.created_at) }}</span>
                    </div>
                    <span class="rounded-full bg-primary/10 px-2 py-0.5 font-medium text-primary">
                        {{ formattedFileSize }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bottom border accent that appears on hover -->
        <div class="h-1 w-0 bg-gradient-to-r from-primary to-primary/50 transition-all duration-500 group-hover:w-full" />
    </Link>
</template>
