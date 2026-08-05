<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowRight, Presentation, BookOpen, Sparkles } from '@lucide/vue';
</script>

<template>
    <section class="container bg-white relative overflow-hidden">

        <div class="grid place-items-center lg:max-w-screen-xl gap-8 mx-auto py-20 md:py-32 relative z-10">
            <div class="text-center space-y-8 animate-fade-in-up">
                <Badge class="text-sm py-2 px-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-0 inline-flex items-center gap-2 shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 hover:scale-105 transition-all duration-300 animate-bounce-in">
                    <Sparkles class="h-4 w-4 animate-pulse" />
                    Free E-Learning Platform
                </Badge>

                <div class="max-w-screen-md mx-auto text-center text-5xl md:text-6xl font-bold animate-fade-in-up animation-delay-200">
                    <h1 class="text-gray-900 tracking-tight">
                        <span class="inline-block hover:scale-110 transition-transform duration-300 cursor-default">Matuto.</span>{' '}
                        <span class="inline-block hover:scale-110 transition-transform duration-300 cursor-default">Maintindihan.</span>
                        <br />
                        <span
                            class="text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 bg-clip-text inline-block hover:scale-110 transition-transform duration-300 cursor-default animate-gradient bg-[length:200%_auto]"
                        >
                            Makipag-usap.
                        </span>
                    </h1>
                </div>

                <p class="max-w-screen-sm mx-auto text-xl text-gray-600 animate-fade-in-up animation-delay-400 leading-relaxed">
                    GRAMATECHA is your free learning platform for <strong class="text-blue-700 font-semibold">Pasulat at Pasalitang Komunikasyon</strong> - with reading materials and video lessons grounded in the standards of the <strong class="text-blue-700 font-semibold">Komisyon sa Wikang Filipino</strong>.
                </p>

                <div class="space-y-4 md:space-y-0 md:space-x-4 animate-fade-in-up animation-delay-600">
                    <Button as-child class="w-5/6 md:w-auto font-bold group/arrow bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-300 hover:shadow-xl hover:shadow-blue-400 hover:scale-105 transition-all duration-300">
                        <Link href="/presentations">
                            <BookOpen class="mr-2 h-5 w-5 group-hover/arrow:rotate-12 transition-transform" />
                            Start Reading Lessons
                            <ArrowRight
                                class="size-5 ml-2 group-hover/arrow:translate-x-1 transition-transform"
                            />
                        </Link>
                    </Button>

                    <Button as-child variant="outline" class="w-5/6 md:w-auto font-bold border-2 border-blue-500 text-blue-700 hover:bg-blue-50 hover:border-blue-600 hover:scale-105 hover:shadow-lg transition-all duration-300 group/video">
                        <Link href="/presentations">
                            <Presentation class="mr-2 h-5 w-5 group-hover/video:scale-110 transition-transform" />
                            Watch Video Lessons
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="relative group mt-14 w-full animate-fade-in-up animation-delay-800">
                <!-- Animated gradient shadows -->
                <div
                    class="absolute -top-6 right-12 w-[90%] h-12 lg:h-[80%] bg-gradient-to-r from-blue-300/30 via-indigo-300/30 to-blue-300/30 blur-3xl rounded-full img-shadow-animation"
                ></div>
                <div
                    class="absolute top-0 left-0 w-[90%] h-[90%] bg-gradient-to-br from-blue-400/10 to-indigo-400/10 blur-2xl rounded-full animate-pulse-slow"
                ></div>

                <div
                    class="w-full md:w-[1200px] mx-auto rounded-3xl relative flex items-center justify-center border-2 border-blue-200 img-border-animation bg-white p-12 shadow-2xl hover:shadow-blue-300/50 transition-all duration-500 hover:scale-[1.02] overflow-hidden group-hover:border-blue-300"
                >
                    <!-- Animated background pattern -->
                    <div class="absolute inset-0 opacity-[0.03]">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(59,130,246,0.3),transparent_50%)] animate-pulse-slow"></div>
                    </div>
                    
                    <Presentation class="size-64 text-blue-400/30 group-hover:text-blue-500/40 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 relative z-10" />
                    
                    <!-- Floating particles -->
                    <div class="absolute top-10 left-20 w-2 h-2 bg-blue-400 rounded-full animate-float opacity-40"></div>
                    <div class="absolute bottom-20 right-32 w-3 h-3 bg-indigo-400 rounded-full animate-float-delayed opacity-40"></div>
                    <div class="absolute top-32 right-20 w-2 h-2 bg-blue-500 rounded-full animate-float-slow opacity-40"></div>
                </div>

                <!-- Enhanced gradient overlay -->
                <div
                    class="absolute bottom-0 left-0 w-full h-32 md:h-40 bg-gradient-to-b from-transparent via-white/80 to-white rounded-lg pointer-events-none"
                ></div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.img-shadow-animation {
    animation: img-shadow-animation 3s ease-in-out infinite alternate;
}

.img-border-animation {
    animation: img-border-animation 3s ease-in-out infinite alternate;
}

@keyframes img-shadow-animation {
    from {
        opacity: 0.4;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 0.7;
        transform: translateY(0px) scale(1);
    }
}

@keyframes img-border-animation {
    from {
        border-color: rgb(219, 234, 254);
    }
    to {
        border-color: rgb(147, 197, 253);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(-20px) translateX(10px);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0) translateX(0) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) translateX(-15px) rotate(5deg);
    }
}

@keyframes float-slow {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-40px) scale(1.05);
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

@keyframes bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.3) translateY(-20px);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.6;
    }
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
}

.animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

.animate-bounce-in {
    animation: bounce-in 1s ease-out forwards;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

.animate-gradient {
    animation: gradient 4s ease infinite;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-400 {
    animation-delay: 0.4s;
}

.animation-delay-600 {
    animation-delay: 0.6s;
}

.animation-delay-800 {
    animation-delay: 0.8s;
}
</style>
