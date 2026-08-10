<script setup lang="ts">
import TopNav from '@/components/TopNav.vue';
import { Toaster } from '@/components/ui/sonner';
</script>

<template>
    <div class="min-h-screen bg-white">
        <TopNav />
        <main>
            <slot />
        </main>
        <Toaster />
    </div>
</template>
