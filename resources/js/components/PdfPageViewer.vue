<script setup lang="ts">
/**
 * PdfPageViewer
 *
 * Renders a PDF file page-by-page using PDF.js (Mozilla).
 * Each page is drawn onto a <canvas> — no browser plugin, no external
 * service, works 100% on localhost.
 */
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import {
    ChevronLeft,
    ChevronRight,
    Download,
    ExternalLink,
    Loader2,
    AlertTriangle,
    Maximize2,
    Minimize2,
    ZoomIn,
    ZoomOut,
} from '@lucide/vue';
import { Button } from '@/components/ui/button';

// ── Props ─────────────────────────────────────────────────────────────────────

const props = defineProps<{
    streamUrl: string;
    downloadUrl: string;
}>();

// ── State ─────────────────────────────────────────────────────────────────────

const canvasRef     = ref<HTMLCanvasElement | null>(null);
const containerRef  = ref<HTMLDivElement | null>(null);
const thumbsRef     = ref<HTMLDivElement | null>(null);

const isLoading     = ref(true);
const error         = ref<string | null>(null);
const currentPage   = ref(1);
const totalPages    = ref(0);
const isFullscreen  = ref(false);
const scale         = ref(1.5);
const isRendering   = ref(false);
const thumbsLoaded  = ref(false);
const thumbDataUrls = ref<string[]>([]);

let pdfDoc: any = null;

// ── Computed ──────────────────────────────────────────────────────────────────

const progress = computed(() =>
    totalPages.value > 0 ? (currentPage.value / totalPages.value) * 100 : 0,
);

// ── Lifecycle ─────────────────────────────────────────────────────────────────

onMounted(async () => {
    await loadPdf();
    window.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', onResize);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', onResize);
    pdfDoc = null;
});

let resizeTimeout: ReturnType<typeof setTimeout> | null = null;
function onResize() {
    if (resizeTimeout) clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        if (pdfDoc) renderPage(currentPage.value);
    }, 150);
}

// ── PDF Loading ───────────────────────────────────────────────────────────────

async function loadPdf() {
    isLoading.value = true;
    error.value     = null;

    try {
        const pdfjsLib = await import('pdfjs-dist');

        // Use the bundled worker (Vite resolves this via import.meta.url)
        pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
            'pdfjs-dist/build/pdf.worker.mjs',
            import.meta.url,
        ).href;

        pdfDoc = await pdfjsLib.getDocument({ url: props.streamUrl }).promise;
        totalPages.value = pdfDoc.numPages;
        isLoading.value  = false;

        await nextTick();
        await renderPage(1);
        renderAllThumbnails(); // async, non-blocking
    } catch (e: any) {
        console.error('[PdfPageViewer] load error:', e);
        error.value = e?.message ?? 'Failed to load PDF.';
        isLoading.value = false;
    }
}

// ── Page Rendering ────────────────────────────────────────────────────────────

async function renderPage(pageNum: number) {
    if (!pdfDoc || !canvasRef.value || isRendering.value) return;
    isRendering.value = true;

    try {
        const page = await pdfDoc.getPage(pageNum);

        // Auto-fit: use container width if available, else fall back to fixed scale
        const containerWidth = containerRef.value?.clientWidth ?? 0;
        let renderScale = scale.value;
        if (containerWidth > 0) {
            const naturalViewport = page.getViewport({ scale: 1 });
            // Fill ~96% of the container width
            renderScale = (containerWidth * 0.96) / naturalViewport.width;
            // Don't go below 0.5 or above 4
            renderScale = Math.min(Math.max(renderScale, 0.5), 4);
        }

        const viewport = page.getViewport({ scale: renderScale });
        const canvas   = canvasRef.value;
        const ctx      = canvas.getContext('2d')!;

        canvas.width  = viewport.width;
        canvas.height = viewport.height;

        await page.render({ canvasContext: ctx, viewport }).promise;
    } finally {
        isRendering.value = false;
    }
}

// ── Thumbnail Generation ──────────────────────────────────────────────────────

async function renderAllThumbnails() {
    if (!pdfDoc || thumbsLoaded.value) return;
    if (!pdfDoc) return;

    thumbsLoaded.value = true;

    const urls: string[] = [];
    const thumbScale = 0.15;

    for (let i = 1; i <= pdfDoc.numPages; i++) {
        try {
            if (!pdfDoc) break;

            const page     = await pdfDoc.getPage(i);
            const viewport = page.getViewport({ scale: thumbScale });

            // Use OffscreenCanvas when available (works in workers + main thread)
            // Fall back to document.createElement only in the main thread
            let canvas: HTMLCanvasElement | OffscreenCanvas;
            let ctx: CanvasRenderingContext2D | OffscreenCanvasRenderingContext2D;

            if (typeof OffscreenCanvas !== 'undefined') {
                canvas = new OffscreenCanvas(Math.ceil(viewport.width), Math.ceil(viewport.height));
                ctx = canvas.getContext('2d') as OffscreenCanvasRenderingContext2D;
            } else {
                canvas = document.createElement('canvas');
                canvas.width  = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                ctx = canvas.getContext('2d') as CanvasRenderingContext2D;
            }

            await page.render({ canvasContext: ctx as any, viewport }).promise;

            if (canvas instanceof OffscreenCanvas) {
                const blob = await canvas.convertToBlob({ type: 'image/jpeg', quality: 0.65 });
                urls.push(URL.createObjectURL(blob));
            } else {
                urls.push((canvas as HTMLCanvasElement).toDataURL('image/jpeg', 0.65));
            }
        } catch {
            urls.push('');
        }
    }

    thumbDataUrls.value = urls;
}

// ── Navigation ────────────────────────────────────────────────────────────────

async function goToPage(pageNum: number) {
    if (pageNum < 1 || pageNum > totalPages.value || isRendering.value) return;
    currentPage.value = pageNum;
    await renderPage(pageNum);

    // Scroll the thumbnail into view
    await nextTick();
    const el = thumbsRef.value?.children[pageNum - 1] as HTMLElement | undefined;
    el?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

async function prev() { await goToPage(currentPage.value - 1); }
async function next() { await goToPage(currentPage.value + 1); }

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') next();
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   prev();
    if (e.key === 'Escape' && isFullscreen.value)         isFullscreen.value = false;
}

// ── Zoom ──────────────────────────────────────────────────────────────────────

async function zoomIn()  { scale.value = Math.min(scale.value + 0.3, 3.0); await renderPage(currentPage.value); }
async function zoomOut() { scale.value = Math.max(scale.value - 0.3, 0.5); await renderPage(currentPage.value); }
</script>

<template>
    <!-- ── Loading ───────────────────────────────────────────────────────────── -->
    <div v-if="isLoading" class="flex h-[80vh] flex-col items-center justify-center gap-3">
        <Loader2 class="h-10 w-10 animate-spin text-primary" />
        <p class="text-sm text-muted-foreground">Loading PDF…</p>
    </div>

    <!-- ── Error ─────────────────────────────────────────────────────────────── -->
    <div v-else-if="error" class="flex h-[400px] flex-col items-center justify-center gap-4 p-8 text-center">
        <AlertTriangle class="h-12 w-12 text-amber-500" />
        <div>
            <p class="font-semibold text-foreground">Could not load PDF</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ error }}</p>
        </div>
        <Button as-child variant="outline">
            <a :href="downloadUrl" download>
                <Download class="mr-2 h-4 w-4" />
                Download PDF
            </a>
        </Button>
    </div>

    <!-- ── PDF Viewer ─────────────────────────────────────────────────────────── -->
    <div
        v-else
        :class="[
            'flex flex-col',
            isFullscreen ? 'fixed inset-0 z-50 bg-background' : 'relative',
        ]"
    >
        <!-- ── Toolbar ─────────────────────────────────────────────────── -->
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-sidebar-border/70 bg-card px-4 py-2">

            <!-- Page navigation -->
            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" :disabled="currentPage <= 1 || isRendering" @click="prev">
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <span class="min-w-[8rem] text-center text-sm text-muted-foreground">
                    Page <strong class="text-foreground">{{ currentPage }}</strong> / {{ totalPages }}
                </span>
                <Button variant="outline" size="sm" :disabled="currentPage >= totalPages || isRendering" @click="next">
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>

            <!-- Zoom + Fullscreen -->
            <div class="flex items-center gap-1">
                <Button variant="ghost" size="sm" :disabled="isRendering" @click="zoomOut" title="Zoom out">
                    <ZoomOut class="h-4 w-4" />
                </Button>
                <span class="w-12 text-center text-xs text-muted-foreground">{{ Math.round(scale * 100) }}%</span>
                <Button variant="ghost" size="sm" :disabled="isRendering" @click="zoomIn" title="Zoom in">
                    <ZoomIn class="h-4 w-4" />
                </Button>
                <div class="mx-1 h-4 w-px bg-border" />
                <Button variant="ghost" size="sm" @click="isFullscreen = !isFullscreen" :title="isFullscreen ? 'Exit fullscreen' : 'Fullscreen'">
                    <Maximize2 v-if="!isFullscreen" class="h-4 w-4" />
                    <Minimize2 v-else class="h-4 w-4" />
                </Button>
            </div>

            <!-- Open / Download -->
            <div class="flex gap-2">
                <Button as-child variant="outline" size="sm">
                    <a :href="streamUrl" target="_blank" rel="noopener noreferrer">
                        <ExternalLink class="mr-1.5 h-3.5 w-3.5" />
                        Open
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

        <!-- Progress bar -->
        <div class="h-0.5 w-full bg-muted/40">
            <div
                class="h-full bg-primary transition-all duration-300 ease-out"
                :style="{ width: progress + '%' }"
            />
        </div>

        <!-- ── Canvas area ─────────────────────────────────────────────── -->
        <div
            ref="containerRef"
            class="relative flex items-start justify-center overflow-auto bg-muted/40 py-4"
            :class="isFullscreen ? 'flex-1' : 'min-h-[80vh]'"
        >
            <!-- Rendering spinner overlay -->
            <div
                v-if="isRendering"
                class="absolute inset-0 z-10 flex items-center justify-center bg-background/40 backdrop-blur-sm"
            >
                <Loader2 class="h-8 w-8 animate-spin text-primary" />
            </div>

            <!-- Prev arrow -->
            <button
                v-if="currentPage > 1"
                @click="prev"
                :disabled="isRendering"
                class="sticky top-1/2 mr-4 -translate-y-1/2 self-start flex-shrink-0 rounded-full bg-card p-2.5 shadow-lg border border-sidebar-border/70 transition-all hover:bg-accent hover:scale-110 disabled:opacity-40"
            >
                <ChevronLeft class="h-5 w-5" />
            </button>

            <!-- The PDF canvas -->
            <canvas
                ref="canvasRef"
                class="w-full rounded shadow-2xl ring-1 ring-black/10"
                style="max-width: 100%; height: auto;"
            />

            <!-- Next arrow -->
            <button
                v-if="currentPage < totalPages"
                @click="next"
                :disabled="isRendering"
                class="sticky top-1/2 ml-4 -translate-y-1/2 self-start flex-shrink-0 rounded-full bg-card p-2.5 shadow-lg border border-sidebar-border/70 transition-all hover:bg-accent hover:scale-110 disabled:opacity-40"
            >
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>

        <!-- ── Thumbnail strip ─────────────────────────────────────────── -->
        <div
            v-if="totalPages > 1"
            ref="thumbsRef"
            class="flex gap-2 overflow-x-auto border-t border-sidebar-border/70 bg-card p-3"
        >
            <!-- Thumbnail images rendered via data URL -->
            <button
                v-for="(url, i) in thumbDataUrls"
                :key="i"
                @click="goToPage(i + 1)"
                :class="[
                    'flex-shrink-0 overflow-hidden rounded border-2 transition-all',
                    i + 1 === currentPage
                        ? 'border-primary shadow-md scale-105'
                        : 'border-transparent hover:border-muted-foreground/40',
                ]"
                :title="`Page ${i + 1}`"
            >
                <img
                    v-if="url"
                    :src="url"
                    alt=""
                    class="block h-auto w-20 object-cover"
                    draggable="false"
                />
                <div v-else class="flex h-14 w-20 items-center justify-center bg-muted text-xs text-muted-foreground">
                    {{ i + 1 }}
                </div>
            </button>

            <!-- Placeholder dots while thumbnails generate -->
            <template v-if="thumbDataUrls.length === 0">
                <div
                    v-for="i in Math.min(totalPages, 30)"
                    :key="`ph-${i}`"
                    :class="[
                        'flex h-14 w-20 flex-shrink-0 items-center justify-center rounded border-2 text-xs text-muted-foreground transition-all',
                        i === currentPage ? 'border-primary bg-primary/10' : 'border-transparent bg-muted/50',
                    ]"
                    @click="goToPage(i)"
                    role="button"
                >
                    {{ i }}
                </div>
            </template>
        </div>
    </div>
</template>
