<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import Footer from '@/components/landing/Footer.vue';
import { useScrollReveal, useStaggerReveal } from '@/composables/useScrollReveal';

defineOptions({ layout: PublicLayout });

const developer = {
    name: 'Jancee Kenn E. Abonita',
    role: 'Web Developer & Programmer',
    school: 'Camarines Sur Polytechnic Colleges, Nabua',
    photo: '/about/JANCEE KENN E. ABONITA.jpg',
    photoScale: 1.35,
    bio: 'Si Jancee Kenn E. Abonita ay isang Software Developer at Programmer mula sa Camarines Sur Polytechnic Colleges (CSPC), Nabua. Siya ang nanguna sa buong disenyo at pagbuo ng Gramatecha — mula sa istruktura ng sistema hanggang sa hitsura ng website. May malalim siyang kaalaman at karanasan sa larangan ng Web at Software Development.',
    links: [
        { icon: '📧', label: 'abonita.jancee1@gmail.com', href: 'mailto:abonita.jancee1@gmail.com' },
        { icon: '💼', label: 'github.com/jkabonita', href: 'https://github.com/jkabonita' },
        { icon: '📘', label: 'facebook.com/theonlyjancee', href: 'https://www.facebook.com/theonlyjancee' },
        { icon: '📸', label: 'instagram.com/theonlyjancee', href: 'https://instagram.com/theonlyjancee' },
    ],
};

const authors = [
    {
        name: 'John Paul M. Quiding',
        school: 'Central Bicol State University of Agriculture, Pili',
        photo: '/about/JOHN PAUL M. QUIDING.jpg',
        bio: "Ang awtor na ito ay si John Paul M. Quiding, dalawampu't isang taong gulang, isinilang noong ika-2 ng Hunyo taong 2005 sa Cadlan, Pili, Camarines Sur, at kasalukuyang naninirahan sa Hacienda Salamat, Cadlan, Pili, Camarines Sur. Siya ay ikaanim na anak nina Raquel M. Quiding at Dongingo C. Quiding Jr. Kasalukuyang nasa ikaapat na taon na sa kolehiyo at kumukuha ng kursong Batsilyer ng Edukasyong Pansekundarya Medyor sa Filipino sa Central Bicol State University of Agriculture.",
        links: [
            { icon: '📧', label: 'johnpaul.quiding@cbsua.edu.ph', href: 'mailto:johnpaul.quiding@cbsua.edu.ph' },
            { icon: '📘', label: 'facebook.com/johnpaul.quiding', href: 'https://www.facebook.com/johnpaul.quiding.9' },
            { icon: '📸', label: 'instagram.com/im_paulliee', href: 'https://www.instagram.com/im_paulliee?igsh=YmNiamM4a3M3czVy' },
        ],
    },
    {
        name: 'Julie Ann L. Cañon',
        school: 'Central Bicol State University of Agriculture, Pili',
        photo: '/about/JULIE ANN L. CAÑON.jpg',
        bio: "Ang awtor na ito ay si Julie Ann L. Cañon, dalawampu't tatlong taong gulang, isinilang noong ika-30 ng Hunyo taong 2003 sa Cararayan, Naga City, Camarines Sur, at kasalukuyang naninirahan sa San Rafael, Cararayan, Naga City, Camarines Sur. Siya ay ikalawang na anak nina Lebita L. Cañon at Rico B. Cañon (+). Kasalukuyang nasa ikaapat na taon na sa kolehiyo at kumukuha ng kursong Batsilyer ng Edukasyong Pansekundarya Medyor sa Filipino sa Central Bicol State University of Agriculture.",
        links: [
            { icon: '📧', label: 'julieann.canon@cbsua.edu.ph', href: 'mailto:julieann.canon@cbsua.edu.ph' },
            { icon: '📘', label: 'facebook.com/julieann', href: 'https://www.facebook.com/profile.php?id=61578942831327' },
        ],
    },
    {
        name: 'Mila E. Alcala',
        school: 'Central Bicol State University of Agriculture, Pili',
        photo: '/about/MILA E. ALCALA.png',
        bio: "Ang awtor na ito ay si Mila E. Alcala, dalawampu't isang taong gulang, isinilang noong ika-18 ng Hunyo taong 2005 sa Bula, Camarines Sur, at kasalukuyang naninirahan sa Sta. Elena, Bula, Camarines Sur. Siya ang bunso sa anim na anak nina Meriel N. Alcala at Fe E. Alcala. Nagtapos siya ng elementarya sa Sta. Elena Elementary School bilang With Honors at kasalukuyang nasa ikaapat na taon na sa kolehiyo at kumukuha ng kursong Batsilyer ng Edukasyong Pansekundarya Medyor sa Filipino sa Central Bicol State University of Agriculture.",
        links: [
            { icon: '📧', label: 'mila.alcala@cbsua.edu.ph', href: 'mailto:mila.alcala@cbsua.edu.ph' },
            { icon: '📘', label: 'facebook.com/milagrowsss', href: 'https://www.facebook.com/milagrowsss' },
            { icon: '📸', label: 'instagram.com/milagrows', href: 'https://www.instagram.com/milagrows' },
        ],
    },
    {
        name: 'Myla T. Villanueva',
        school: 'Central Bicol State University of Agriculture, Pili',
        photo: '/about/MYLA T. VILLANUEVA.png',
        bio: "Ang mananaliksik na ito ay si Myla T. Villanueva, dalawampu't isang taong gulang, isinilang noong ika-7 ng Marso taong 2005 sa Ocampo, Camarines Sur, at kasalukuyang naninirahan sa San Francisco, Ocampo, Camarines Sur. Siya ang bunso sa apat na anak nina Dionisio G. Villanueva at Milagros T. Villanueva. Kasalukuyang nasa ikaapat na taon na sa kolehiyo at kumukuha ng kursong Batsilyer ng Edukasyong Pansekundarya Medyor sa Filipino sa Central Bicol State University of Agriculture.",
        links: [
            { icon: '📧', label: 'myla.villanueva@cbsua.edu.ph', href: 'mailto:myla.villanueva@cbsua.edu.ph' },
            { icon: '📘', label: 'facebook.com/myla.villanueva.104', href: 'https://www.facebook.com/myla.villanueva.104' },
            { icon: '📸', label: 'instagram.com/mylssmile', href: 'https://www.instagram.com/mylssmile' },
        ],
    },
];

// ── Animation refs ────────────────────────────────────────────────────────────
const heroSection    = ref<HTMLElement | null>(null);
const authorsGrid    = ref<HTMLElement | null>(null);
const developerCard  = ref<HTMLElement | null>(null);
const storySection   = ref<HTMLElement | null>(null);
const curriculumGrid = ref<HTMLElement | null>(null);
const kwfBox         = ref<HTMLElement | null>(null);
const stepsGrid      = ref<HTMLElement | null>(null);
const audienceGrid   = ref<HTMLElement | null>(null);
const missionBox     = ref<HTMLElement | null>(null);
const refsSection    = ref<HTMLElement | null>(null);

// Hero elements stagger
const heroBadge    = ref<HTMLElement | null>(null);
const heroHeading  = ref<HTMLElement | null>(null);
const heroSubtitle = ref<HTMLElement | null>(null);

// Authors cards — staggered
useStaggerReveal(authorsGrid, '.author-card');

// Developer card, story, etc.
useScrollReveal(developerCard);
useScrollReveal(storySection);
useStaggerReveal(curriculumGrid, '.curriculum-card');
useScrollReveal(kwfBox);
useStaggerReveal(stepsGrid, '.step-card');
useStaggerReveal(audienceGrid, '.audience-card');
useScrollReveal(missionBox, { threshold: 0.2 });
useScrollReveal(refsSection);
</script>

<template>
    <Head title="About — Gramatecha" />

    <div class="min-h-screen bg-white">

        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 text-white text-center px-4 py-20">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgba(255,255,255,0.12),transparent_70%)]" />

            <!-- Floating orbs (same as landing Hero) -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="orb orb-1" />
                <div class="orb orb-2" />
            </div>

            <div ref="heroBadge" class="hero-item hero-item-0 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest mb-5 backdrop-blur-sm">
                📌 About the Platform
            </div>
            <h1 ref="heroHeading" class="hero-item hero-item-1 text-4xl sm:text-5xl font-black tracking-tight mb-4">
                About <span class="text-yellow-300">GRAMATECHA</span>
            </h1>
            <p ref="heroSubtitle" class="hero-item hero-item-2 mx-auto max-w-md text-white/90 text-base">
                Isang platform para sa bawat mag-aaral na gustong matuto ng wastong komunikasyon sa Filipino.
            </p>
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg viewBox="0 0 1440 60" preserveAspectRatio="none" class="block h-12 w-full fill-white">
                    <path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" />
                </svg>
            </div>
        </section>

        <!-- ══ AUTHORS + DEVELOPER ════════════════════════════ -->
        <div class="bg-gray-50 px-4 pt-14 pb-14">
            <div class="mx-auto max-w-5xl">

                <!-- Authors -->
                <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Ang Mga May-Akda</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Mga Manunulat ng Nilalaman</h2>
                <p class="text-gray-500 text-sm mb-8">Ang mga estudyante na nagbuo ng mga aralin at nilalaman ng Gramatecha.</p>

                <div ref="authorsGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-10">
                    <div
                        v-for="author in authors"
                        :key="author.name"
                        class="author-card reveal-up relative bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-200"
                    >
                        <!-- CBSUA watermark logo -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-[0.15]">
                            <img src="/cbsua.png" alt="CBSUA" class="w-36 h-36 object-contain" />
                        </div>

                        <!-- Photo -->
                        <div class="relative z-10 h-52 w-full overflow-hidden bg-green-50">
                            <img
                                :src="author.photo"
                                :alt="author.name"
                                class="h-full w-full object-cover object-top"
                            />
                        </div>

                        <!-- Info -->
                        <div class="relative z-10 p-4">
                            <h3 class="font-bold text-gray-900 text-sm mb-0.5">{{ author.name }}</h3>
                            <p class="text-[10px] text-green-700 font-semibold mb-2 leading-tight">{{ author.school }}</p>
                            <p class="text-xs text-gray-500 leading-relaxed mb-3">{{ author.bio }}</p>
                            <div class="flex flex-col gap-1.5">
                                <a
                                    v-for="link in author.links"
                                    :key="link.href"
                                    :href="link.href"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-2 text-xs text-green-700 hover:text-green-900 hover:underline truncate"
                                >
                                    <span class="flex-shrink-0">{{ link.icon }}</span>
                                    <span class="truncate">{{ link.label }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 mb-8" />

                <!-- Developer -->
                <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Website Developer</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Ang Nagbuo ng Gramatecha</h2>
                <p class="text-gray-500 text-sm mb-6">Ang developer na responsable sa buong teknikal na pagbuo ng platform.</p>

                <div ref="developerCard" class="reveal-up relative flex flex-col sm:flex-row gap-8 items-center bg-white border border-green-200 rounded-2xl p-8 shadow-sm overflow-hidden">
                    <!-- CSPC watermark logo -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 opacity-[0.15]">
                        <img src="/cspc.png" alt="CSPC" class="w-52 h-52 object-contain" />
                    </div>

                    <!-- Photo -->
                    <div class="relative z-10 h-52 w-44 flex-shrink-0 overflow-hidden rounded-2xl shadow-md bg-green-50">
                        <img
                            :src="developer.photo"
                            :alt="developer.name"
                            class="h-full w-full object-cover object-top"
                            :style="`transform: scale(${developer.photoScale}); transform-origin: top center;`"
                        />
                    </div>

                    <!-- Info -->
                    <div class="relative z-10 flex-1 text-center sm:text-left">
                        <span class="inline-block rounded-full bg-green-700 text-white text-xs font-semibold px-3 py-1 mb-3">
                            💻 {{ developer.role }}
                        </span>
                        <h3 class="text-xl font-extrabold text-gray-900 mb-0.5">{{ developer.name }}</h3>
                        <p class="text-xs text-green-700 font-semibold mb-1">{{ developer.school }}</p>
                        <p class="text-[10px] text-gray-400 font-medium mb-4">Camarines Sur Polytechnic Colleges, Nabua</p>
                        <p class="text-sm text-gray-600 leading-relaxed mb-5">{{ developer.bio }}</p>
                        <div class="flex flex-wrap justify-center sm:justify-start gap-3">
                            <a
                                v-for="link in developer.links"
                                :key="link.href"
                                :href="link.href"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-medium text-green-800 hover:bg-green-700 hover:text-white hover:border-green-700 transition-colors"
                            >
                                <span>{{ link.icon }}</span>
                                <span>{{ link.label }}</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══ PLATFORM INFO ══════════════════════════════════════════════ -->
        <div class="mx-auto max-w-5xl px-4 py-16 space-y-16">

            <!-- Our Story -->
            <div ref="storySection" class="reveal-up grid gap-10 md:grid-cols-2 items-center">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Our Story</p>
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Isang Platform Para sa Bawat Mag-aaral</h2>
                    <p class="text-gray-600 mb-3">
                        GRAMATECHA was built with one goal in mind: to make learning <strong>Pasulat at Pasalitang Komunikasyon</strong> accessible, clear, and relevant for every Filipino student.
                    </p>
                    <p class="text-gray-600 mb-3">
                        Language is the foundation of everything — how you express your ideas, how you write your thoughts, how you connect with other people. And yet, so many students struggle not because they lack the ability, but because the available resources are hard to follow.
                    </p>
                    <p class="text-gray-600 font-medium">We built GRAMATECHA to change that.</p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-green-700 to-emerald-600 p-10 text-white text-center shadow-lg">
                    <div class="mb-4 flex justify-center">
                        <img src="/ph.png" alt="Philippines" class="h-24 w-24 object-contain drop-shadow-lg" />
                    </div>
                    <h3 class="text-xl font-bold mb-2">Para sa mga Pilipinong Estudyante</h3>
                    <p class="text-white/80 text-sm">Modern. Libre. Batay sa KWF.</p>
                </div>
            </div>

            <!-- What We Teach -->
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Curriculum</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-6">What We Teach</h2>
                <div ref="curriculumGrid" class="grid gap-5 sm:grid-cols-2">
                    <div class="curriculum-card reveal-up rounded-xl bg-white border border-gray-200 shadow-sm p-6">
                        <div class="text-3xl mb-3">✍️</div>
                        <h3 class="font-bold text-green-700 mb-1">Pasulat na Komunikasyon</h3>
                        <p class="text-xs text-gray-400 mb-3">Written Communication</p>
                        <ul class="list-disc list-inside text-sm text-gray-500 space-y-1">
                            <li>Pormal at impormal na pagsulat</li>
                            <li>Mga uri ng tekstong nakasulat</li>
                            <li>Gramatika at tamang gamit ng salita</li>
                            <li>Pagsulat ng liham, ulat, at iba pang dokumento</li>
                        </ul>
                    </div>
                    <div class="curriculum-card reveal-up rounded-xl bg-white border border-gray-200 shadow-sm p-6">
                        <div class="text-3xl mb-3">🗣️</div>
                        <h3 class="font-bold text-green-700 mb-1">Pasalitang Komunikasyon</h3>
                        <p class="text-xs text-gray-400 mb-3">Oral Communication</p>
                        <ul class="list-disc list-inside text-sm text-gray-500 space-y-1">
                            <li>Mga elemento ng epektibong pagsasalita</li>
                            <li>Pagbibigay ng talumpati at presentasyon</li>
                            <li>Pakikinig at pakikipag-usap</li>
                            <li>Mga estratehiya sa komunikasyon</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KWF Alignment -->
            <div ref="kwfBox" class="reveal-up rounded-xl bg-green-50 border border-green-200 p-8">
                <h3 class="font-bold text-green-800 text-lg mb-3">🏛️ Our Alignment with the KWF</h3>
                <p class="text-gray-600 text-sm mb-4">
                    All content on GRAMATECHA is based on the official language standards set by the
                    <strong>Komisyon sa Wikang Filipino (KWF)</strong> — the national government agency responsible
                    for developing, preserving, and promoting the Filipino language.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2 text-sm text-gray-700"><span>✅</span> Terminolohiya na nakakaisa sa opisyal na pamantayan</li>
                    <li class="flex items-start gap-2 text-sm text-gray-700"><span>✅</span> Mga halimbawa na nakaangkop sa modernong paggamit ng Filipino</li>
                    <li class="flex items-start gap-2 text-sm text-gray-700"><span>✅</span> Wastong gramatika ayon sa KWF na mga alituntunin</li>
                </ul>
            </div>

            <!-- How It Works -->
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Platform Guide</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-6">How the Platform Works</h2>
                <div ref="stepsGrid" class="grid gap-4 sm:grid-cols-4">
                    <div
                        v-for="(step, i) in [
                            { n: '1', title: 'Browse Lessons', desc: 'Find a topic you want to learn from the full lesson catalog.' },
                            { n: '2', title: 'Choose Your Format', desc: 'Read the lesson, watch the video, or do both — your call.' },
                            { n: '3', title: 'Learn at Your Pace', desc: 'No deadlines. No pressure. Come back whenever you need.' },
                            { n: '4', title: 'Apply It', desc: 'Use the examples and activities built into each lesson.' },
                        ]"
                        :key="i"
                        class="step-card reveal-up rounded-xl bg-white border border-gray-200 shadow-sm p-5 text-center"
                    >
                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-green-700 text-white font-bold text-lg">
                            {{ step.n }}
                        </div>
                        <h4 class="font-bold text-gray-800 mb-1 text-sm">{{ step.title }}</h4>
                        <p class="text-xs text-gray-500">{{ step.desc }}</p>
                    </div>
                </div>
            </div>

            <!-- Who It's For -->
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-green-600 mb-1">Who It's For</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-5">GRAMATECHA is For…</h2>
                <div ref="audienceGrid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="a in [
                            { icon: '🎓', text: 'Senior High School students studying Oral and Written Communication' },
                            { icon: '🎓', text: 'College students enrolled in Filipino communication subjects' },
                            { icon: '📚', text: 'Anyone who wants to improve their written and spoken Filipino' },
                            { icon: '👨‍🏫', text: 'Teachers looking for supplementary learning materials' },
                        ]"
                        :key="a.text"
                        class="audience-card reveal-up rounded-xl bg-green-50 border border-green-100 p-5 text-sm text-gray-700"
                    >
                        <div class="text-2xl mb-2">{{ a.icon }}</div>
                        {{ a.text }}
                    </div>
                </div>
            </div>

            <!-- Mission -->
            <div ref="missionBox" class="reveal-up rounded-2xl bg-gradient-to-br from-green-700 to-emerald-600 text-white text-center p-10">
                <h3 class="text-xs font-semibold uppercase tracking-widest opacity-75 mb-3">Our Mission</h3>
                <p class="text-lg italic max-w-2xl mx-auto opacity-95">
                    "To give every Filipino student a clear, reliable, and modern learning experience in Pasulat at Pasalitang Komunikasyon — guided by the standards of the Komisyon sa Wikang Filipino."
                </p>
            </div>
        </div>

        <!-- References -->
        <div ref="refsSection" class="reveal-up border-t border-gray-200 mt-12 pt-10 pb-10 px-4">
            <div class="mx-auto max-w-3xl">
                <h2 class="text-center text-xl font-extrabold uppercase tracking-widest text-red-700 mb-8">References</h2>
                <ol class="space-y-5 text-sm text-gray-700 leading-relaxed list-none">
                    <li>Almario, V. S., (2014). <em>KWF Manwal sa Masinop na Pagsulat.</em> Komisyon sa Wikang Filipino.</li>
                    <li>Austero et al., (2009). <em>Komunikasyon sa Akademikong Filipino (Binagong edisyon).</em> Unlad Publishing House.</li>
                    <li>Bernales et al., (2009). <em>Mabisang Komunikasyon sa Wikang Pang-akademiko (Edisyong 2009).</em> Mutya Publishing House, Inc.</li>
                    <li>Buenaventura, M. S. &amp; Valdez, P. N. V., (2016). <em>Sining ng Komunikasyon.</em> Rex Book Store.</li>
                    <li>Capili, J., (2016). <em>Gabay sa Malikhaing Pagsulat.</em> University of the Philippines Press.</li>
                    <li>Frigillana, D. M., (2008). <em>Retorika at Komunikasyon.</em> National Book Store.</li>
                    <li>Komisyon sa Wikang Filipino. (2014). <em>Ortograpiya ng Wikang Filipino.</em> KWF.</li>
                    <li>Nolasco, R. M. D., (2010). <em>Wika at Kultura.</em> University of the Philippines Press.</li>
                    <li>Rubrico, J. G. U., (2007). <em>The Philippine Language and Communication.</em> National Book Store.</li>
                    <li>Villafuerte, L. &amp; Aganan, F., (2000). <em>Gabay sa Komunikasyon sa Akademikong Filipino.</em> Mutya Publishing House, Inc.</li>
                </ol>
            </div>
        </div>

        <Footer />
    </div>
</template>

<style scoped>
/* ── Hero entrance (same pattern as Hero.vue) ────────────────────────────── */
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

/* ── Floating orbs ────────────────────────────────────────────────────────── */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.22;
    animation: float 8s ease-in-out infinite;
}
.orb-1 {
    width: 280px; height: 280px;
    background: radial-gradient(circle, #86efac, transparent);
    top: -60px; left: -40px;
    animation-delay: 0s;
}
.orb-2 {
    width: 200px; height: 200px;
    background: radial-gradient(circle, #fde68a, transparent);
    top: 20px; right: -30px;
    animation-delay: -4s;
}
@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); }
    50%       { transform: translateY(-18px) scale(1.04); }
}

/* ── Scroll-reveal base ───────────────────────────────────────────────────── */
.reveal-up {
    opacity: 0;
    transform: translateY(32px);
    transition:
        opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    transition-delay: calc(var(--stagger, 0) * 90ms);
}

.is-visible {
    opacity: 1 !important;
    transform: none !important;
}
</style>
