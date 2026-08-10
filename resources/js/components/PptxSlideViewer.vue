<script setup lang="ts">
/**
 * PptxSlideViewer
 *
 * Fetches slide data from /presentations/{id}/slides (JSON),
 * then renders each slide as a styled card in a slide-show viewer.
 * Works 100% on localhost — no external services needed.
 */
import { ref, computed, onMounted, watch } from 'vue';
import { ChevronLeft, ChevronRight, Download, ExternalLink, Loader2, AlertTriangle, Maximize2, Minimize2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface Slide {
    index: number;
    title: string;
    texts: string[];
    bg_color: string;
    has_image: boolean;
}

interface SlidesResponse {
    title: string;
    slides: Slide[];
    error?: string;
    message?: string;
}

const props = defineProps<{
    presentationId: number;
    downloadUrl: string;
    streamUrl: string;
}>();

// ── State ─────────────────────────────────────────────────────────────────────

const slides = ref<Slide[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);
const currentIndex = ref(0);
const isFullscreen = ref(false);

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(async () => {
    await fetchSlides();
});

async function fetchSlides() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await fetch(`/presentations/${props.presentationId}/slides`);
        const data: SlidesResponse = await response.json();

        if (!response.ok || data.error) {
            error.value = data.message || data.error || 'Failed to load slides.';
            return;
        }

        slides.value = data.slides;
        currentIndex.value = 0;
    } catch (e) {
        error.value = 'Network error — could not load slides.';
    } finally {
        isLoading.value = false;
    }
}

// ── Computed ──────────────────────────────────────────────────────────────────

const currentSlide = computed<Slide | null>(() => slides.value[currentIndex.value] ?? null);
const totalSlides = computed(() => slides.value.length);
const progress = computed(() =>
    totalSlides.value > 0 ? ((currentIndex.value + 1) / totalSlides.value) * 100 : 0,
);

/** Determine readable text color from hex background */
function textColorFor(hex: string): string {
    const clean = hex.replace('#', '');
    const r = parseInt(clean.substring(0, 2), 16);
    const g = parseInt(clean.substring(2, 4), 16);
    const b = parseInt(clean.substring(4, 6), 16);
    // Luminance formula
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#1a1a1a' : '#ffffff';
}

/** Subtle lighter/darker shade for body text */
function bodyColorFor(hex: string): string {
    const base = textColorFor(hex);
    return base === '#ffffff' ? 'rgba(255,255,255,0.80)' : 'rgba(0,0,0,0.65)';
}

// ── Navigation ────────────────────────────────────────────────────────────────

function prev() {
    if (currentIndex.value > 0) currentIndex.value--;
}

function next() {
    if (currentIndex.value < totalSlides.value - 1) currentIndex.value++;
}

function goTo(index: number) {
    currentIndex.value = index;
}

// Keyboard navigation
function onKeydown(e: KeyboardEvent) {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next();
    if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') prev();
    if (e.key === 'Escape' && isFullscreen.value) isFullscreen.value = false;
}

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
});

import { onUnmounted } from 'vue';
onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <!-- Loading -->
    <div v-if="isLoading" class="flex h-[500px] items-center justify-center">
        <Loader2 class="h-8 w-8 animate-spin text-primary" />
        <span class="ml-3 text-muted-foreground">Extracting slides…</span>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="flex h-[400px] flex-col items-center justify-center gap-4 p-8 text-center">
        <AlertTriangle class="h-12 w-12 text-amber-500" />
        <div>
            <p class="font-semibold text-foreground">Could not render slides</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ error }}</p>
        </div>
        <Button as-child variant="outline">
            <a :href="downloadUrl" download>
                <Download class="mr-2 h-4 w-4" />
                Download to view in PowerPoint
            </a>
        </Button>
    </div>

    <!-- No slides -->
    <div v-else-if="totalSlides === 0" class="flex h-[400px] flex-col items-center justify-center gap-4 p-8 text-center">
        <p class="text-muted-foreground">No slides found in this presentation.</p>
        <Button as-child variant="outline">
            <a :href="downloadUrl" download>
                <Download class="mr-2 h-4 w-4" />
                Download File
            </a>
        </Button>
    </div>

    <!-- ── Slide Viewer ───────────────────────────────────────────────────── -->
    <div
        v-else
        :class="[
            'flex flex-col',
            isFullscreen
                ? 'fixed inset-0 z-50 bg-black'
                : 'relative'
        ]"
    >
        <!-- ── Slide Stage ────────────────────────────────────────────────── -->
        <div
            :class="[
                'relative flex items-center justify-center overflow-hidden',
                isFullscreen ? 'flex-1' : 'aspect-[16/9] w-full'
            ]"
            :style="{ backgroundColor: currentSlide?.bg_color ?? '#1B3A5C' }"
        >
            <!-- Subtle grid overlay for visual depth -->
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.04]"
                style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 40px 40px;"
            />

            <!-- Accent bar on left -->
            <div
                class="absolute left-0 top-0 h-full w-1.5 opacity-60"
                :style="{ background: `linear-gradient(to bottom, ${currentSlide?.bg_color ?? '#1B3A5C'}, white, ${currentSlide?.bg_color ?? '#1B3A5C'})` }"
            />

            <!-- Slide number badge -->
            <div
                class="absolute right-4 top-4 rounded-full px-2.5 py-0.5 text-xs font-medium backdrop-blur-sm"
                :style="{
                    background: 'rgba(255,255,255,0.12)',
                    color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C'),
                    border: '1px solid rgba(255,255,255,0.15)',
                }"
            >
                {{ currentIndex + 1 }} / {{ totalSlides }}
            </div>

            <!-- Fullscreen toggle -->
            <button
                @click="isFullscreen = !isFullscreen"
                class="absolute left-4 top-4 rounded-full p-1.5 transition-colors backdrop-blur-sm"
                :style="{
                    background: 'rgba(255,255,255,0.12)',
                    color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C'),
                }"
                title="Toggle fullscreen"
            >
                <Maximize2 v-if="!isFullscreen" class="h-4 w-4" />
                <Minimize2 v-else class="h-4 w-4" />
            </button>

            <!-- Slide Content -->
            <div
                class="relative z-10 flex w-full flex-col justify-center px-16 py-12"
                :class="isFullscreen ? 'h-full max-w-5xl mx-auto' : ''"
            >
                <!-- Title -->
                <h2
                    class="mb-6 font-bold leading-tight tracking-tight"
                    :class="isFullscreen ? 'text-5xl' : 'text-3xl'"
                    :style="{ color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C') }"
                >
                    {{ currentSlide?.title }}
                </h2>

                <!-- Body text blocks -->
                <div class="space-y-3">
                    <p
                        v-for="(text, i) in currentSlide?.texts ?? []"
                        :key="i"
                        class="leading-relaxed whitespace-pre-line"
                        :class="isFullscreen ? 'text-xl' : 'text-base'"
                        :style="{ color: bodyColorFor(currentSlide?.bg_color ?? '#1B3A5C') }"
                    >
                        {{ text }}
                    </p>
                </div>

                <!-- Image indicator -->
                <p
                    v-if="currentSlide?.has_image && (currentSlide?.texts?.length ?? 0) === 0"
                    class="mt-4 text-sm italic opacity-60"
                    :style="{ color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C') }"
                >
                    [This slide contains image content]
                </p>
            </div>

            <!-- Prev / Next overlay arrows -->
            <button
                v-if="currentIndex > 0"
                @click="prev"
                class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full p-2 transition-all backdrop-blur-sm hover:scale-110"
                :style="{
                    background: 'rgba(255,255,255,0.15)',
                    color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C'),
                }"
            >
                <ChevronLeft class="h-6 w-6" />
            </button>
            <button
                v-if="currentIndex < totalSlides - 1"
                @click="next"
                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full p-2 transition-all backdrop-blur-sm hover:scale-110"
                :style="{
                    background: 'rgba(255,255,255,0.15)',
                    color: textColorFor(currentSlide?.bg_color ?? '#1B3A5C'),
                }"
            >
                <ChevronRight class="h-6 w-6" />
            </button>
        </div>

        <!-- ── Progress Bar ───────────────────────────────────────────────── -->
        <div class="h-1 w-full bg-muted/30">
            <div
                class="h-full bg-primary transition-all duration-300"
                :style="{ width: progress + '%' }"
            />
        </div>

        <!-- ── Controls Bar ───────────────────────────────────────────────── -->
        <div
            :class="[
                'flex items-center justify-between gap-4 px-4 py-3',
                isFullscreen
                    ? 'bg-black/80 text-white'
                    : 'border-t border-sidebar-border/70 bg-card'
            ]"
        >
            <!-- Slide navigation -->
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" :disabled="currentIndex === 0" @click="prev">
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <span class="min-w-[5rem] text-center text-sm text-muted-foreground">
                    {{ currentIndex + 1 }} / {{ totalSlides }}
                </span>
                <Button variant="outline" size="sm" :disabled="currentIndex === totalSlides - 1" @click="next">
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>

            <!-- Slide dots (max 20 shown) -->
            <div class="hidden items-center gap-1 sm:flex flex-wrap justify-center">
                <button
                    v-for="(_, i) in slides.slice(0, 20)"
                    :key="i"
                    @click="goTo(i)"
                    :class="[
                        'rounded-full transition-all',
                        i === currentIndex
                            ? 'w-4 h-2 bg-primary'
                            : 'w-2 h-2 bg-muted-foreground/30 hover:bg-muted-foreground/60'
                    ]"
                    :title="`Slide ${i + 1}`"
                />
                <span v-if="totalSlides > 20" class="text-xs text-muted-foreground ml-1">+{{ totalSlides - 20 }}</span>
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <Button as-child variant="outline" size="sm">
                    <a :href="streamUrl" target="_blank" rel="noopener noreferrer">
                        <ExternalLink class="mr-1.5 h-3.5 w-3.5" />
                        Raw
                    </a>
                </Button>
                <Button as-child size="sm">
                    <a :href="downloadUrl" download>
                        <Download class="mr-1.5 h-3.5 w-3.5" />
                        Download
                    </a>
                </Button>
            </div>
        </div>

        <!-- ── Thumbnail strip ────────────────────────────────────────────── -->
        <div
            v-if="!isFullscreen && totalSlides > 1"
            class="flex gap-2 overflow-x-auto border-t border-sidebar-border/70 bg-muted/20 p-3 scrollbar-thin"
        >
            <button
                v-for="(slide, i) in slides"
                :key="slide.index"
                @click="goTo(i)"
                :class="[
                    'flex-shrink-0 rounded-md overflow-hidden border-2 transition-all w-28 h-16',
                    i === currentIndex
                        ? 'border-primary shadow-md scale-105'
                        : 'border-transparent hover:border-muted-foreground/40'
                ]"
                :title="slide.title"
            >
                <div
                    class="flex h-full w-full flex-col items-start justify-start p-1.5"
                    :style="{ backgroundColor: slide.bg_color }"
                >
                    <p
                        class="text-[7px] font-bold leading-tight line-clamp-2 text-left"
                        :style="{ color: textColorFor(slide.bg_color) }"
                    >
                        {{ slide.title }}
                    </p>
                </div>
            </button>
        </div>
    </div>
</template>
