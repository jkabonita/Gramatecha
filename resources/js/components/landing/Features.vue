<script setup lang="ts">
import { ref } from 'vue';
import { useScrollReveal, useStaggerReveal } from '@/composables/useScrollReveal';

const features = [
    {
        icon: '📖',
        title: 'Magbasa sa Sariling Bilis',
        desc: 'Mga nakaayos na babasahin tungkol sa gramatikang Filipino, pasulat na komunikasyon, at pamantayan ng wika — kahit saan, kahit kailan.',
    },
    {
        icon: '🎬',
        title: 'Manood at Matuto',
        desc: 'Mga aralin sa pamamagitan ng video na nagpapaliwanag ng mahahalagang konsepto nang malinaw, na may mga halimbawang magagamit sa tunay na buhay.',
    },
    {
        icon: '📋',
        title: 'Batay sa Pamantayan ng KWF',
        desc: 'Ang lahat ng nilalaman ay naaayon sa opisyal na alituntunin ng Komisyon sa Wikang Filipino — upang matiyak na tama ang iyong natuttunan.',
    },
];

const lessons = [
    { title: 'Ano ang Pasalitang Komunikasyon?', format: '📖 Read · 🎬 Watch', level: 'Panimula', color: 'green' },
    { title: 'Mga Uri ng Pasulat na Komunikasyon', format: '📖 Read · 🎬 Watch', level: 'Panimula', color: 'green' },
    { title: 'Komunikasyon ayon sa KWF', format: '📖 Read', level: 'Intermediate', color: 'yellow' },
    { title: 'Pagsulat ng Pormal na Liham', format: '📖 Read · 🎬 Watch', level: 'Intermediate', color: 'yellow' },
    { title: 'Epektibong Pagsasalita', format: '🎬 Watch', level: 'Panimula', color: 'green' },
    { title: 'Mga Pagkakamali sa Pasulat na Filipino', format: '📖 Read', level: 'Intermediate', color: 'yellow' },
];

const badgeClass: Record<string, string> = {
    green: 'bg-green-100 text-green-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    red: 'bg-red-100 text-red-800',
};

const quotes = [
    {
        text: '"Noon, nahihirapan ako sa pagsulat ng mga pormal na dokumento. Ngayon, mas malinaw na ang lahat sa akin — thanks to GRAMATECHA."',
        author: '🎓 Estudyante, 2nd Year College',
    },
    {
        text: '"Ang mga video lessons dito ay mas madaling intindihin kaysa sa mga textbook. Sobrang helpful lalo na para sa mga visual learner."',
        author: '🎓 Senior High School Student',
    },
    {
        text: `"Natutunan ko dito ang tamang paraan ng pakikipag-usap sa iba't ibang sitwasyon. Highly recommended talaga!"`,
        author: '🎓 3rd Year Education Major',
    },
];

// ── Refs for scroll-reveal sections ─────────────────────────────────────────
const featureStrip   = ref<HTMLElement | null>(null);
const whyHeading     = ref<HTMLElement | null>(null);
const whyList        = ref<HTMLElement | null>(null);  // ol container
const quoteRow1      = ref<HTMLElement | null>(null);
const quoteRow2      = ref<HTMLElement | null>(null);
const quoteRow3      = ref<HTMLElement | null>(null);
const quoteRow4      = ref<HTMLElement | null>(null);
const lessonsSection = ref<HTMLElement | null>(null);
const testimonialsEl = ref<HTMLElement | null>(null);
const ctaBanner      = ref<HTMLElement | null>(null);

// Feature cards — staggered fade-up
useStaggerReveal(featureStrip, '.reveal-card');

// "Why" heading — simple reveal
useScrollReveal(whyHeading);

// "Why" numbered list — staggered slide-in on the <li> children
useStaggerReveal(whyList, 'li');

// Quote rows — each row revealed independently
useScrollReveal([quoteRow1, quoteRow2, quoteRow3, quoteRow4], { threshold: 0.12 });

// Lessons table
useScrollReveal(lessonsSection);

// Testimonials — staggered cards
useStaggerReveal(testimonialsEl, '.reveal-card');

// CTA banner
useScrollReveal(ctaBanner, { threshold: 0.2 });
</script>

<template>
    <!-- ── Feature Strip ─────────────────────────────────────────────── -->
    <section class="bg-white py-16 px-4">
        <div ref="featureStrip" class="mx-auto max-w-5xl grid gap-6 sm:grid-cols-3">
            <div
                v-for="f in features"
                :key="f.title"
                class="reveal-card reveal-up rounded-2xl bg-green-50 border border-green-100 p-8 text-center transition-shadow hover:shadow-md hover:-translate-y-1 duration-200"
            >
                <div class="text-4xl mb-3">{{ f.icon }}</div>
                <h3 class="font-bold text-green-800 mb-2">{{ f.title }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ f.desc }}</p>
            </div>
        </div>
    </section>

    <!-- ── Why GRAMATECHA ─────────────────────────────────────────────── -->
    <section ref="whySection" class="py-16 px-4">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">About the Platform</p>
            <h2 ref="whyHeading" class="reveal-up text-3xl font-extrabold text-gray-900 mb-3">Bakit GRAMATECHA?</h2>
            <p class="text-gray-700 max-w-2xl mb-8">
                Ang Gramatecha ay isang makabagong educational website na nakatuon sa
                pagpapalalim ng kaalaman sa wikang Filipino, pasulat at pasalitang komunikasyon, at balarila
                o gramatika. Pinagsasama nito ang batayang alituntunin ng wika at ang kapangyarihan ng
                modernong teknolohiya upang magbigay ng accessible at komprehensibong gabay sa pagkatuto.
            </p>

            <ol ref="whyList" class="space-y-5">
                <li
                    v-for="(_, idx) in 3"
                    :key="idx"
                    class="reveal-card reveal-left flex gap-4 items-start"
                    :style="`--stagger: ${idx}`"
                >
                    <span class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-green-700 text-white font-bold text-sm">{{ idx + 1 }}</span>
                    <p class="text-gray-700 leading-relaxed pt-0.5">
                        <template v-if="idx === 0">
                            Nakatutok ito sa buong saklaw ng wika—mula sa ponolohiya, morpolohiya, at
                            ortograpiya, hanggang sa praktikal na aplikasyon sa mabisang pagsulat at pagsasalita.
                        </template>
                        <template v-else-if="idx === 1">
                            Ginagawang mas makabago, dinamiko, at madaling maunawaan ang mga pormal na
                            aralin sa wika sa tulong ng mga visual diagrams, audio guides, at instructional videos.
                        </template>
                        <template v-else>
                            Hindi lamang ito nakadisenyo para pumasa sa pagsusulit, inuunawa nito ang mga
                            sikolohikal at pisikal na salik ng komunikasyon upang tulungan ang bawat mag-aaral
                            na maging mahusay na tagapagsalita at manunulat.
                        </template>
                    </p>
                </li>
            </ol>
        </div>
    </section>

    <!-- ── Photo Quotes ──────────────────────────────────────────────── -->
    <section ref="quotesSection" class="py-14 px-4 bg-white">
        <div class="mx-auto max-w-4xl space-y-14">

            <!-- Row 1 — photo LEFT, quote RIGHT -->
            <div ref="quoteRow1" class="reveal-up flex flex-col sm:flex-row items-center gap-8">
                <div class="flex-shrink-0 w-64 overflow-hidden rounded-xl shadow-md border border-gray-200 bg-white">
                    <img src="/home/Home1.png" alt="Home 1" class="w-full h-auto object-contain" />
                </div>
                <blockquote class="flex-1 italic text-gray-700 text-base sm:text-lg leading-relaxed">
                    "Dumating na ang panahon sa atin upang magkaraoon ng isang pambansang wika. Hangga't hindi nagkakaroon niyon, tayo'y hindi magiging isang bayan."
                    <footer class="mt-3 not-italic text-sm font-semibold text-green-700">— Manuel L. Quezon</footer>
                </blockquote>
            </div>

            <!-- Row 2 — quote LEFT, photo RIGHT -->
            <div ref="quoteRow2" class="reveal-up flex flex-col sm:flex-row-reverse items-center gap-8">
                <div class="flex-shrink-0 w-64 overflow-hidden rounded-xl shadow-md border border-gray-200 bg-white">
                    <img src="/home/Home2.png" alt="Home 2" class="w-full h-auto object-contain" />
                </div>
                <blockquote class="flex-1 italic text-gray-700 text-base sm:text-lg leading-relaxed">
                    "Kung ano ang wika, iyon ang pagkatao mo. Sa larangang pambansa, ang wika ng bayan ay ang bayan… ang karanasan at kaalaman ng gumagamit na bayan."
                    <footer class="mt-3 not-italic text-sm font-semibold text-green-700">— Virgilio S. Almario</footer>
                </blockquote>
            </div>

            <!-- Row 3 — photo LEFT, quote RIGHT -->
            <div ref="quoteRow3" class="reveal-up flex flex-col sm:flex-row items-center gap-8">
                <div class="flex-shrink-0 w-64 overflow-hidden rounded-xl shadow-md border border-gray-200 bg-white">
                    <img src="/home/Home3.png" alt="Home 3" class="w-full h-auto object-contain" />
                </div>
                <blockquote class="flex-1 italic text-gray-700 text-base sm:text-lg leading-relaxed">
                    "Talking (Speaking) is one of the creative arts, for by it, you build up things that have, until talked about, no existence."
                    <footer class="mt-3 not-italic text-sm font-semibold text-green-700">— Lord Chesterfield</footer>
                </blockquote>
            </div>

            <!-- Row 4 — quote LEFT, photo RIGHT -->
            <div ref="quoteRow4" class="reveal-up flex flex-col sm:flex-row-reverse items-center gap-8">
                <div class="flex-shrink-0 w-64 overflow-hidden rounded-xl shadow-md border border-gray-200 bg-white">
                    <img src="/home/Home4.png" alt="Home 4" class="w-full h-auto object-contain" />
                </div>
                <blockquote class="flex-1 italic text-gray-700 text-base sm:text-lg leading-relaxed">
                    "Beneath the rule of men entirely great, the pen is mightier than the sword."
                    <footer class="mt-3 not-italic text-sm font-semibold text-green-700">— Bulwer-Lytton</footer>
                </blockquote>
            </div>

        </div>
    </section>

    <!-- ── Featured Lessons ──────────────────────────────────────────── -->
    <section ref="lessonsSection" class="reveal-up bg-gray-50 py-16 px-4">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Featured Lessons</p>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Start Learning Today</h2>
            <p class="text-gray-500 mb-8">Pick a lesson and dive in — reading materials and video guides are available for every topic.</p>

            <div class="overflow-hidden rounded-xl shadow-md">
                <table class="w-full bg-white text-sm">
                    <thead>
                        <tr class="bg-green-700 text-white">
                            <th class="px-5 py-3 text-left font-semibold">Aralin</th>
                            <th class="px-5 py-3 text-left font-semibold">Format</th>
                            <th class="px-5 py-3 text-left font-semibold">Antas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(l, i) in lessons"
                            :key="i"
                            class="border-b border-gray-100 hover:bg-green-50 transition-colors"
                        >
                            <td class="px-5 py-3 text-gray-800">{{ l.title }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ l.format }}</td>
                            <td class="px-5 py-3">
                                <span :class="['inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold', badgeClass[l.color]]">
                                    {{ l.level }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                <a
                    href="/presentations"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-green-800 transition-colors"
                >
                    Tingnan ang mga Aralin →
                </a>
                <a
                    href="/videos"
                    class="inline-flex items-center gap-2 rounded-lg border-2 border-green-700 px-5 py-2.5 text-sm font-bold text-green-700 hover:bg-green-700 hover:text-white transition-colors"
                >
                    Panoorin ang mga Aralin →
                </a>
            </div>
        </div>
    </section>

    <!-- ── Testimonials ──────────────────────────────────────────────── -->
    <section class="py-16 px-4">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Student Testimonials</p>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8">What Students Are Saying</h2>
            <div ref="testimonialsEl" class="grid gap-5 sm:grid-cols-3">
                <div
                    v-for="q in quotes"
                    :key="q.author"
                    class="reveal-card reveal-up rounded-xl bg-white border-l-4 border-green-500 p-6 shadow-sm hover:shadow-md transition-shadow duration-200"
                >
                    <p class="text-sm italic text-gray-700 mb-4">{{ q.text }}</p>
                    <div class="text-xs font-semibold text-gray-400">{{ q.author }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA Banner ────────────────────────────────────────────────── -->
    <section class="px-4 pb-16">
        <div
            ref="ctaBanner"
            class="reveal-up mx-auto max-w-4xl rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-400 p-10 text-center shadow-xl"
        >
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-2">Handa ka na bang magsimula?</h2>
            <p class="text-white/90 mb-6">GRAMATECHA is completely free. Just open a lesson — read it, watch it, and learn at your own pace.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a
                    href="/presentations"
                    class="rounded-lg bg-gray-900 px-6 py-3 text-sm font-bold text-white hover:bg-black transition-colors"
                >
                    📚 Browse All Lessons
                </a>
                <a
                    href="/videos"
                    class="rounded-lg border-2 border-white/70 bg-transparent px-6 py-3 text-sm font-bold text-white hover:bg-white/15 transition-colors"
                >
                    🎬 Watch Free Videos
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ── Shared reveal base ───────────────────────────────────────────────────── */
/* Elements start invisible; the IntersectionObserver adds .is-visible         */
.reveal-up {
    opacity: 0;
    transform: translateY(36px);
    transition:
        opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

.reveal-left {
    opacity: 0;
    transform: translateX(-28px);
    transition:
        opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
}

/* Stagger: each card gets --stagger from JS, adds 90 ms per item */
.reveal-card.reveal-up,
.reveal-card.reveal-left {
    transition-delay: calc(var(--stagger, 0) * 90ms);
}

/* Visible state — added by useScrollReveal / useStaggerReveal */
.is-visible {
    opacity: 1 !important;
    transform: none !important;
}
</style>
