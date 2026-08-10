<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted/30 p-6 md:p-10"
    >
        <div class="w-full max-w-md">
            <div class="flex flex-col gap-8 rounded-xl border bg-card p-8 shadow-lg md:p-10">
                <div class="flex flex-col items-center gap-6">
                    <Link
                        href="/"
                        class="flex flex-col items-center gap-3 font-medium transition-opacity hover:opacity-80"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary"
                        >
                            <AppLogoIcon
                                class="size-8 fill-current text-primary-foreground"
                            />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
                        <p class="text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
