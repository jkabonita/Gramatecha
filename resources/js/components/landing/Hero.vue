<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Video, ArrowRight } from '@lucide/vue';
import { ref, onMounted } from 'vue';

// ── Entrance animation state ─────────────────────────────────────────────────
// Each element starts hidden; we add `visible` class after the component mounts
// to trigger a staggered CSS animation.
const mounted = ref(false);
onMounted(() => {
    // Small tick so the browser paints the initial (hidden) state first
    requestAnimationFrame(() => {
        mounted.value = true;
    });
});
</script>

<template>
    <!-- Hero -->
    <section
        class="relative overflow-hidden bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 text-white text-center px-4 py-24"
    >
        <!-- Animated floating orbs -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="orb orb-1" />
            <div class="orb orb-2" />
            <div class="orb orb-3" />
        </div>

        <!-- Subtle radial glow -->
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(255,255,255,0.12),transparent_70%)]"
        />

        <!-- Badge -->
        <div
            :class="['hero-item', { visible: mounted }]"
            style="--delay: 0"
            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest mb-6 backdrop-blur-sm"
        >
            📚 Free E-Learning Platform
        </div>

        <!-- Headline -->
        <h1
            :class="['hero-item', { visible: mounted }]"
            style="--delay: 1"
            class="mx-auto max-w-3xl text-4xl sm:text-5xl lg:text-6xl font-black leading-tight tracking-tight"
        >
            <span class="text-yellow-300">GRAMATECHA</span><br />
            Gabay sa<br />
            Makabagong Komunikasyong Filipino
        </h1>

        <!-- Sub-heading -->
        <p
            :class="['hero-item', { visible: mounted }]"
            style="--delay: 2"
            class="mx-auto mt-6 max-w-2xl text-base sm:text-lg text-white/90 leading-relaxed"
        >
            Ang Gramatecha ay isang makabagong educational website na nakatuon sa
            pagpapalalim ng kaalaman sa wikang Filipino, pasulat at pasalitang komunikasyon, at balarila
            o gramatika. Pinagsasama nito ang batayang alituntunin ng wika at ang kapangyarihan ng
            modernong teknolohiya upang magbigay ng accessible at komprehensibong gabay sa pagkatuto.
        </p>

        <!-- CTA Buttons -->
        <div
            :class="['hero-item', { visible: mounted }]"
            style="--delay: 3"
            class="mt-10 flex flex-wrap justify-center gap-4"
        >
            <Link
                href="/presentations"
                class="group inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-bold text-green-700 shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl"
            >
                <BookOpen class="h-5 w-5 transition-transform group-hover:rotate-12" />
                Magbasa ng Aralin
                <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
            </Link>

            <Link
                href="/videos"
                class="inline-flex items-center gap-2 rounded-lg border-2 border-white/60 bg-transparent px-6 py-3 text-sm font-bold text-white backdrop-blur-sm transition-all duration-200 hover:border-white hover:bg-white/10"
            >
                <Video class="h-5 w-5" />
               Manood ng Aralin
            </Link>
        </div>

        <!-- Wave divider -->
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
            <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="block h-12 w-full fill-white">
                <path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" />
            </svg>
        </div>
    </section>
</template>

<style scoped>
/* ── Hero entrance animations ─────────────────────────────────────────────── */
.hero-item {
    opacity: 0;
    transform: translateY(28px);
    transition:
        opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: calc(var(--delay, 0) * 120ms);
}

.hero-item.visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Floating orbs ──────────────────────────────────────────────────────────── */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.25;
    animation: float 8s ease-in-out infinite;
}

.orb-1 {
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, #86efac, transparent);
    top: -80px;
    left: -60px;
    animation-delay: 0s;
}

.orb-2 {
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, #fde68a, transparent);
    top: 40px;
    right: -40px;
    animation-delay: -3s;
}

.orb-3 {
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, #6ee7b7, transparent);
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    animation-delay: -5s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); }
    50%       { transform: translateY(-20px) scale(1.05); }
}
</style>
