<script setup lang="ts">
import PublicNav from '@/components/PublicNav.vue';
import { Toaster } from '@/components/ui/sonner';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

// ── Scroll progress bar ───────────────────────────────────────────────────────
const scrollProgress = ref(0);

function updateProgress() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    scrollProgress.value = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
}

onMounted(() => window.addEventListener('scroll', updateProgress, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', updateProgress));

// ── Page transition via Inertia router events ─────────────────────────────────
// We cannot use Vue <Transition> here because Inertia page components often
// have multiple root nodes (e.g. <Head> + <div>), which Vue 3 Transition
// does not support. Instead we animate the <main> wrapper directly.
const mainEl = ref<HTMLElement | null>(null);

function triggerPageIn() {
    if (!mainEl.value) return;
    mainEl.value.classList.remove('page-in');
    void mainEl.value.offsetWidth; // force reflow so animation restarts
    mainEl.value.classList.add('page-in');
}

// Fire on first load
onMounted(triggerPageIn);

// Fire on every Inertia navigation finish
const offFinish = router.on('finish', triggerPageIn);
onUnmounted(offFinish);
</script>

<template>
    <div class="flex flex-col min-h-screen bg-white overflow-x-hidden">
        <!-- Scroll progress indicator -->
        <div
            class="scroll-bar"
            :style="{ width: scrollProgress + '%' }"
            aria-hidden="true"
        />

        <PublicNav />

        <main ref="mainEl" class="flex flex-1 flex-col min-w-0">
            <slot />
        </main>

        <Toaster />
    </div>
</template>

<style scoped>
/* ── Scroll progress bar ──────────────────────────────────────────────────── */
.scroll-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #15803d, #4ade80, #fde68a);
    z-index: 9999;
    transition: width 0.1s linear;
    border-radius: 0 2px 2px 0;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.6);
}

/* ── Page transition ──────────────────────────────────────────────────────── */
/* Applied to <main> on mount and after every Inertia navigation */
.page-in {
    animation: page-fade-up 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
}

@keyframes page-fade-up {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
