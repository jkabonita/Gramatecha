<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Hero from '@/components/landing/Hero.vue';
import Features from '@/components/landing/Features.vue';
import Footer from '@/components/landing/Footer.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout
});
</script>

<template>
    <Head title="Welcome to Gramatecha" />
    
    <div class="min-h-screen bg-gradient-to-b from-white to-green-50">
        <Hero />
        <Features />
        <Footer />
    </div>
</template>
