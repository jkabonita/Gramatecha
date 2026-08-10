<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Footer from '@/components/landing/Footer.vue';
import { useScrollReveal, useStaggerReveal } from '@/composables/useScrollReveal';

defineOptions({ layout: PublicLayout });

interface FaqItem {
    q: string;
    a: string;
}
interface FaqCategory {
    title: string;
    items: FaqItem[];
}

const categories: FaqCategory[] = [
    // ── Subject-specific categories ─────────────────────────────────────────
    {
        title: '📌 Pangkalahatang Impormasyon',
        items: [
            {
                q: 'Ano ang Gramatecha at para kanino ito?',
                a: `Ang Gramatecha ay isang makabagong educational website na nakatuon sa pagtuturo ng wikang Filipino, Balarila (Gramatika), at Pasulat at Pasalitang Komunikasyon. Binuo ito para sa mga:
                <ul class="list-disc list-inside mt-2 space-y-1">
                  <li>Mag-aaral na gustong mapabuti ang kanilang kakayahan sa wika</li>
                  <li>Mga guro na naghahanap ng supplementary materials</li>
                  <li>Mga propesyonal na gustong magpakadalubhasa at magkaroon ng kumpiyansa sa paggamit ng wika</li>
                </ul>`,
            },
            {
                q: 'Libre ba ang paggamit ng mga materyales sa Gramatecha?',
                a: `Ang karamihan sa mga batayang aralin, sanggunian sa gramatika, at mga gabay na video ay <strong>libreng maa-access ng lahat ng bisita</strong>. Mayroon ding mga nakatalagang interactive quizzes at downloadable resources na magagamit para sa mas malalim na pag-aaral.`,
            },
        ],
    },
    {
        title: '📚 Wikang Filipino at Balarila (Grammar)',
        items: [
            {
                q: 'Ano ang pangunahing pagkakaiba ng Tagalog, Pilipino, at Filipino?',
                a: `<ul class="space-y-2 mt-1">
                  <li><strong>Tagalog:</strong> Ang katutubong wika na naging batayan ng pambansang wika noong 1937.</li>
                  <li><strong>Pilipino:</strong> Ang tawag sa pambansang wika simula noong 1959 batay sa Kautusang Pangkagawaran Blg. 7.</li>
                  <li><strong>Filipino:</strong> Ang opisyal na pambansang wika ng Pilipinas ayon sa 1987 Konstitusyon (Artikulo XIV, Seksyon 6). Ito ay buhay na wika na patuloy na yumayabong sa pamamagitan ng paghiram sa iba't ibang wika sa Pilipinas at banyagang wika.</li>
                </ul>`,
            },
            {
                q: 'Bakit idinagdag ang walong (8) bagong titik (C, F, J, Ñ, Q, V, X, Z) sa Alpabetong Filipino?',
                a: `Idinagdag ang walong titik para sa modernisasyon at estandardisasyon ng wika. Ginagamit ang mga ito sa:
                <ul class="list-disc list-inside mt-2 space-y-1">
                  <li>Pantanging ngalan (hal. <em>Valenzuela, Quezon, Cecilia</em>)</li>
                  <li>Mga salitang katutubo mula sa ibang wika sa Pilipinas (hal. <em>cañao, vakul, masjid</em>)</li>
                  <li>Mga salitang teknikal, siyentipiko, o may natatanging kulturang kahulugan</li>
                </ul>`,
            },
            {
                q: 'Paano malalaman kung kailan gagamitin ang gitling (-)?',
                a: `Ginagamit ang gitling sa mga sumusunod na pagkakataon:
                <ul class="list-disc list-inside mt-2 space-y-1">
                  <li>Sa pag-uulit ng salitang-ugat (hal. <em>araw-araw</em>)</li>
                  <li>Kapag ang unlapi ay nagtatapos sa katinig at ang salitang nilalapian ay nagsisimula sa patinig (hal. <em>pag-ibig, tag-init</em>)</li>
                  <li>Kapag may unlapi ang tanging ngalan (hal. <em>maka-Pilipino, taga-Baguio</em>)</li>
                  <li>Kapag ang panlaping <em>ika-</em> ay ikinakabit sa numero (hal. <em>ika-3 ng hapon</em>)</li>
                </ul>`,
            },
        ],
    },
    {
        title: '✍️ Pasulat na Komunikasyon',
        items: [
            {
                q: 'Ano-ano ang mga yugto sa proseso ng mabisang pagsulat?',
                a: `Ang pagsulat ay dumadaan sa apat (4) na pangunahing yugto:
                <ol class="list-decimal list-inside mt-2 space-y-2">
                  <li><strong>Bago Sumulat (Pre-writing)</strong> — Pagpili ng paksa, pangangalap ng datos, at pagbuo ng balangkas.</li>
                  <li><strong>Pagsulat (Drafting)</strong> — Pagbuo ng unang burador o draft gamit ang payak at malinaw na pangungusap.</li>
                  <li><strong>Pagwawasto (Editing/Revising)</strong> — Pagsusuri sa estilo, gramatika, bantas, at kawastuhan ng mga ideya.</li>
                  <li><strong>Muling Pagsulat (Rewriting)</strong> — Pagbuo ng malinis at pinal na kopya na handa nang ilathala.</li>
                </ol>`,
            },
        ],
    },
    {
        title: '🗣️ Pasalitang Komunikasyon',
        items: [
            {
                q: 'Paano ko malalabanan ang "Stage Fright" o takot sa pagsasalita sa publiko?',
                a: `Ang takot sa pagsasalita (glossophobia) ay likas kahit sa mga may karanasan na. Maaari itong maibsan sa pamamagitan ng:
                <ul class="list-disc list-inside mt-2 space-y-2">
                  <li><strong>Sapat na Paghahanda at Pagsasanay</strong> — Magpraktis sa harap ng salamin o sa maliliit na grupo.</li>
                  <li><strong>Epektibong Paghinga</strong> — Gumamit ng tamang paghinga sa tulong ng dayapram bago magsimula upang marelaks ang boses at katawan.</li>
                  <li><strong>Positibong Atityud</strong> — Isiping kaibigan at hindi mapanghusga ang iyong mga tagapakinig.</li>
                </ul>`,
            },
            {
                q: 'Ano ang mga pangunahing kasangkapan sa mabisang pagsasalita?',
                a: `Ang bisa ng tagapagsalita ay nakasalalay sa limang (5) pangunahing kasangkapan:
                <ol class="list-decimal list-inside mt-2 space-y-1">
                  <li><strong>Tinig</strong> — Angkop na lakas, himig, at tono.</li>
                  <li><strong>Bigkas</strong> — Malinaw at matatas na pagbigkas sa mga salita.</li>
                  <li><strong>Tindig</strong> — Maayos at kaaya-ayang postura sa entablado.</li>
                  <li><strong>Kumpas</strong> — Natural na galaw ng kamay na umaayon sa sinasabi.</li>
                  <li><strong>Kilos</strong> — Angkop na galaw ng mata, ulo, at katawan habang nagsasalita.</li>
                </ol>`,
            },
        ],
    },
    // ── Platform & support categories ────────────────────────────────────────
    {
        title: '🖥️ About the Platform',
        items: [
            { q: 'Libre ba ang GRAMATECHA?', a: 'Yes — completely free. All reading materials and video lessons on GRAMATECHA are available at no cost. No registration, no subscription, no hidden fees.' },
            { q: 'Kailangan ko bang mag-sign up para magamit ang platform?', a: 'Hindi na. You can browse and access all lessons without creating an account. Just go to the Lessons page and start learning.' },
            { q: 'Anong devices ang maaaring gamitin para sa GRAMATECHA?', a: "GRAMATECHA works on any device — desktop, laptop, tablet, or mobile phone. As long as you have a browser and internet connection, you're good to go." },
            { q: 'Anong wika ang ginagamit sa mga aralin?', a: 'Most lessons are written and recorded in Filipino, with some concepts explained using a mix of Filipino and English for clarity. This is intentional — so that the content is accessible to more students.' },
        ],
    },
    {
        title: '📖 About the Content',
        items: [
            { q: 'Sino ang gumawa ng mga aralin sa GRAMATECHA?', a: 'Our lessons are developed by educators with backgrounds in Filipino language, communication, and teaching. All content is reviewed against the official guidelines of the Komisyon sa Wikang Filipino (KWF).' },
            { q: 'Tama ba ang mga leksyon dito ayon sa opisyal na pamantayan?', a: 'Yes. All content on GRAMATECHA aligns with KWF standards — the official national language authority of the Philippines. We regularly update our materials to reflect any changes in official language guidelines.' },
            { q: 'Para sa anong level ng pag-aaral ang GRAMATECHA?', a: 'GRAMATECHA is designed primarily for Senior High School and College students, but the content is structured so that anyone — regardless of level — can follow along. Lessons are tagged: Panimula (Beginner), Intermediate, and Advanced.' },
            { q: 'Mayroon bang activities o assessments?', a: 'Some lessons include guided reflection questions and practice activities built into the reading material. We are continuously adding more interactive content to the platform.' },
        ],
    },
    {
        title: '🎬 About Video Lessons',
        items: [
            { q: 'Saan naka-host ang mga video lessons?', a: 'All video lessons are streamed directly on the platform through an embedded video player — no need to be redirected to another site.' },
            { q: 'May mga subtitle o closed captions ba ang mga videos?', a: 'Yes. Most video lessons include Filipino subtitles for accessibility. We are working to add closed captions to all video content.' },
            { q: 'Maaari ko bang i-download ang mga aralin?', a: 'At this time, lessons are only available for online viewing and reading. We are exploring offline access options for future updates.' },
        ],
    },
    {
        title: '🔧 Technical & Support',
        items: [
            { q: 'Hindi gumagana ang isang aralin. Ano ang dapat kong gawin?', a: "Try refreshing your browser or clearing your cache first. If the issue continues, please contact us and we'll look into it right away." },
            { q: 'Maaari ko bang mag-suggest ng bagong paksa?', a: 'Absolutely. We welcome suggestions from students and teachers. Use the Contact page to send us your idea — we read every message.' },
            { q: 'May certificate ba ang GRAMATECHA?', a: 'We are currently working on a certificate feature for students who complete full units. Stay tuned for updates.' },
        ],
    },
];

const openItem = ref<string | null>(null);

const toggle = (key: string) => {
    openItem.value = openItem.value === key ? null : key;
};

// ── Animation refs ────────────────────────────────────────────────────────────
const faqList  = ref<HTMLElement | null>(null);
const ctaBox   = ref<HTMLElement | null>(null);

// Each category block revealed staggered
useStaggerReveal(faqList, '.faq-category');

// CTA box at the bottom
useScrollReveal(ctaBox, { threshold: 0.2 });
</script>

<template>
    <Head title="FAQ — Gramatecha" />

    <div class="min-h-screen bg-white">

        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 text-white text-center px-4 py-20">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(255,255,255,0.12),transparent_70%)]" />

            <!-- Floating orbs -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="orb orb-1" />
                <div class="orb orb-2" />
            </div>

            <div class="hero-item hero-item-0 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest mb-5 backdrop-blur-sm">
                ❓ Help Center
            </div>
            <h1 class="hero-item hero-item-1 text-4xl sm:text-5xl font-black tracking-tight mb-4">
                Mga Madalas na <span class="text-yellow-300">Tanong</span>
            </h1>
            <p class="hero-item hero-item-2 mx-auto max-w-md text-white/90">Got questions? Here are the answers to the ones we hear most often.</p>
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="block h-12 w-full fill-white">
                    <path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" />
                </svg>
            </div>
        </section>

        <!-- FAQ Content -->
        <div class="mx-auto max-w-3xl px-4 py-16 space-y-12">

            <div ref="faqList" class="space-y-12">
                <div
                    v-for="cat in categories"
                    :key="cat.title"
                    class="faq-category reveal-up space-y-3"
                >
                    <h3 class="font-bold text-green-700 border-b-2 border-gray-100 pb-2 text-lg">{{ cat.title }}</h3>

                    <div
                        v-for="item in cat.items"
                        :key="item.q"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
                    >
                        <button
                            class="w-full flex items-center justify-between px-5 py-4 text-left text-sm font-semibold text-gray-800 hover:bg-gray-50 transition-colors"
                            @click="toggle(cat.title + item.q)"
                        >
                            {{ item.q }}
                            <span
                                class="ml-3 flex-shrink-0 text-green-600 transition-transform duration-300"
                                :class="openItem === cat.title + item.q ? 'rotate-180' : ''"
                            >▼</span>
                        </button>

                        <!-- Animated accordion panel -->
                        <Transition name="accordion">
                            <div
                                v-if="openItem === cat.title + item.q"
                                class="px-5 pb-5 pt-3 text-sm text-gray-600 leading-relaxed border-t border-gray-100"
                                v-html="item.a"
                            />
                        </Transition>
                    </div>
                </div>
            </div>

            <!-- Still have questions -->
            <div ref="ctaBox" class="reveal-up rounded-xl bg-green-50 border border-green-200 p-7 text-center">
                <p class="font-semibold text-gray-800 mb-1">Hindi mo pa nakita ang sagot sa iyong tanong?</p>
                <p class="text-sm text-gray-500 mb-4">Send us a message and we'll get back to you within 1–2 business days.</p>
                <Link
                    href="/presentations"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-green-800 transition-colors"
                >
                    📚 Browse Lessons Instead →
                </Link>
            </div>
        </div>

        <Footer />
    </div>
</template>

<style scoped>
/* ── Hero entrance ───────────────────────────────────────────────────────── */
.hero-item {
    opacity: 0;
    transform: translateY(24px);
    animation: hero-in 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
}
.hero-item-0 { animation-delay: 0ms; }
.hero-item-1 { animation-delay: 120ms; }
.hero-item-2 { animation-delay: 240ms; }

@keyframes hero-in {
    to { opacity: 1; transform: translateY(0); }
}

/* ── Floating orbs ───────────────────────────────────────────────────────── */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.22;
    animation: float 8s ease-in-out infinite;
}
.orb-1 {
    width: 260px; height: 260px;
    background: radial-gradient(circle, #86efac, transparent);
    top: -50px; left: -30px;
    animation-delay: 0s;
}
.orb-2 {
    width: 200px; height: 200px;
    background: radial-gradient(circle, #fde68a, transparent);
    top: 10px; right: -20px;
    animation-delay: -3.5s;
}
@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); }
    50%       { transform: translateY(-18px) scale(1.04); }
}

/* ── Scroll-reveal base ──────────────────────────────────────────────────── */
.reveal-up {
    opacity: 0;
    transform: translateY(28px);
    transition:
        opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: calc(var(--stagger, 0) * 80ms);
}

.is-visible {
    opacity: 1 !important;
    transform: none !important;
}

/* ── Accordion transition ────────────────────────────────────────────────── */
.accordion-enter-active {
    animation: accordion-open 0.28s cubic-bezier(0.22, 1, 0.36, 1) both;
}
.accordion-leave-active {
    animation: accordion-open 0.2s cubic-bezier(0.55, 0, 0.45, 1) reverse both;
}

@keyframes accordion-open {
    from {
        opacity: 0;
        transform: translateY(-6px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 600px;
    }
}
</style>
