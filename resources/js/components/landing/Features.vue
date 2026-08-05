<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { BookOpen, Video, FileCheck, GraduationCap, DollarSign, Globe } from '@lucide/vue';

interface FeaturesProps {
    icon: any;
    title: string;
    description: string;
}

const featureList: FeaturesProps[] = [
    {
        icon: BookOpen,
        title: 'Read at Your Pace',
        description:
            'Structured reading materials on Filipino grammar, written communication, and language standards - anytime, anywhere.',
    },
    {
        icon: Video,
        title: 'Watch and Learn',
        description:
            'Video-based lessons that walk you through key concepts clearly, with examples you can actually use in real life.',
    },
    {
        icon: FileCheck,
        title: 'Guided by KWF Standards',
        description:
            'All content aligns with the official guidelines of the Komisyon sa Wikang Filipino - so you\'re learning the right way.',
    },
    {
        icon: GraduationCap,
        title: 'For Filipino Students',
        description:
            'Designed specifically for Senior High School and College students studying Oral and Written Communication in Filipino.',
    },
    {
        icon: DollarSign,
        title: 'Completely Free',
        description:
            'No registration required. No hidden fees. Access all reading materials and video lessons without any cost.',
    },
    {
        icon: Globe,
        title: 'Modern Filipino Content',
        description:
            'Clear, modern lessons - in Filipino, for Filipinos. No confusing textbooks. No outdated content.',
    },
];
</script>

<template>
    <section id="features" class="container py-24 sm:py-32 bg-white relative overflow-hidden">

        <div class="relative z-10">
            <h2 class="text-sm text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-center mb-2 tracking-wider font-bold uppercase animate-fade-in">
                Features
            </h2>

            <h2 class="text-3xl md:text-4xl text-center font-bold mb-4 text-gray-900 animate-fade-in animation-delay-200">
                Bakit <span class="text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text">GRAMATECHA</span>?
            </h2>

            <h3 class="md:w-1/2 mx-auto text-xl text-center text-gray-600 mb-12 animate-fade-in animation-delay-400">
                Communication is a skill you build - not something you're just born with. GRAMATECHA gives you a clear path forward.
            </h3>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="({ icon, title, description }, index) in featureList" 
                    :key="title"
                    class="feature-card animate-fade-in-up"
                    :style="{ animationDelay: `${(index + 1) * 100}ms` }"
                >
                    <Card class="h-full bg-white border-2 border-blue-100 shadow-lg hover:shadow-2xl hover:shadow-blue-200/50 transition-all duration-500 hover:scale-105 hover:-translate-y-2 hover:border-blue-300 group relative overflow-hidden">
                        
                        <CardHeader class="flex justify-center items-center relative z-10">
                            <div
                                class="bg-gradient-to-br from-blue-100 to-indigo-100 p-4 rounded-full ring-8 ring-blue-50 mb-4 group-hover:ring-blue-100 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 relative"
                            >
                                <!-- Animated pulse ring -->
                                <div class="absolute inset-0 bg-blue-400/30 rounded-full animate-ping-slow opacity-0 group-hover:opacity-100"></div>
                                <component :is="icon" class="h-8 w-8 text-blue-600 group-hover:text-indigo-600 transition-colors duration-500 relative z-10 group-hover:scale-110" />
                            </div>

                            <CardTitle class="text-gray-900 group-hover:text-blue-700 transition-colors duration-300 text-center">
                                {{ title }}
                            </CardTitle>
                        </CardHeader>

                        <CardContent class="text-gray-600 text-center relative z-10 group-hover:text-gray-700 transition-colors duration-300">
                            {{ description }}
                        </CardContent>

                        <!-- Corner accent -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400/0 to-indigo-400/0 group-hover:from-blue-400/20 group-hover:to-indigo-400/20 transition-all duration-500 rounded-bl-full"></div>
                    </Card>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.2;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(1.05);
    }
}

@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 0;
    }
    50% {
        opacity: 0.3;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
    opacity: 0;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-ping-slow {
    animation: ping-slow 2s ease-in-out infinite;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-400 {
    animation-delay: 0.4s;
}

.animation-delay-1000 {
    animation-delay: 1s;
}

.feature-card {
    animation-fill-mode: both;
}
</style>
