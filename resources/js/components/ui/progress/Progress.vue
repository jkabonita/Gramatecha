<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = withDefaults(
    defineProps<{
        modelValue?: number;
        class?: HTMLAttributes['class'];
    }>(),
    {
        modelValue: 0,
    },
);

const progressPercentage = computed(() => {
    return Math.min(Math.max(props.modelValue, 0), 100);
});
</script>

<template>
    <div
        :class="
            cn(
                'relative h-2 w-full overflow-hidden rounded-full bg-primary/20',
                props.class,
            )
        "
    >
        <div
            class="h-full bg-primary transition-all duration-300 ease-in-out"
            :style="{ width: `${progressPercentage}%` }"
        />
    </div>
</template>
