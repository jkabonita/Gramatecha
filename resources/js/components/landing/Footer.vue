<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Presentation } from '@lucide/vue';
import { Separator } from '@/components/ui/separator';
</script>

<template>
    <footer id="footer" class="container py-24 pb-16 sm:py-32 sm:pb-24 bg-white relative overflow-hidden">
        <div class="p-10 bg-white border-2 border-blue-100 rounded-3xl shadow-xl hover:shadow-2xl hover:shadow-blue-200/50 transition-all duration-500 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-x-12 gap-y-8">
                <div class="col-span-full xl:col-span-2">
                    <Link href="/" class="flex font-bold items-center group">
                        <div class="relative mr-2">
                            <Presentation
                                class="bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-lg w-9 h-9 border-2 border-blue-400 text-white p-1.5 transition-all duration-300 group-hover:scale-110 group-hover:rotate-12 relative z-10"
                            />
                            <div class="absolute inset-0 bg-blue-400 blur-md rounded-lg opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
                        </div>

                        <h3 class="text-2xl bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent group-hover:from-blue-700 group-hover:to-indigo-700 transition-all duration-300">
                            Gramatecha
                        </h3>
                    </Link>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Pasulat at Pasalitang Komunikasyon - Free learning platform for Filipino students.
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Navigation</h3>
                    <div>
                        <Link href="/" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300">
                            Home
                        </Link>
                    </div>

                    <div>
                        <Link href="/presentations" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300">
                            Lessons
                        </Link>
                    </div>

                    <div>
                        <Link href="/login" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300">
                            Login
                        </Link>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Resources</h3>
                    <div>
                        <a href="#features" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300">
                            Features
                        </a>
                    </div>

                    <div>
                        <a href="#" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300"> 
                            About KWF 
                        </a>
                    </div>

                    <div>
                        <a href="#" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300"> 
                            Support 
                        </a>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">Platform</h3>
                    <div>
                        <a href="#" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300"> 
                            Reading Materials 
                        </a>
                    </div>

                    <div>
                        <a href="#" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300"> 
                            Video Lessons 
                        </a>
                    </div>

                    <div>
                        <a href="#" class="text-gray-600 hover:text-blue-600 hover:translate-x-1 inline-block transition-all duration-300"> 
                            Guidelines 
                        </a>
                    </div>
                </div>
            </div>

            <Separator class="my-6 bg-gradient-to-r from-transparent via-blue-200 to-transparent" />
            <section class="text-center space-y-2">
                <p class="font-bold text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text">
                    GRAMATECHA - Pasulat at Pasalitang Komunikasyon
                </p>
                <p class="text-sm text-gray-600">
                    Lahat ng nilalaman ay batay sa pamantayan ng Komisyon sa Wikang Filipino (KWF).
                </p>
                <p class="text-xs text-gray-500">
                    © {{ new Date().getFullYear() }} GRAMATECHA. Free educational platform for Filipino students.
                </p>
            </section>
        </div>
    </footer>
</template>
