<script setup lang="ts">
import { ScrollAreaCorner, ScrollAreaRoot, ScrollAreaScrollbar, ScrollAreaThumb, ScrollAreaViewport } from 'reka-ui';
import type { ScrollAreaRootProps } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<ScrollAreaRootProps & {
  class?: string;
}>();
</script>

<template>
  <ScrollAreaRoot
    v-bind="props"
    :class="cn('relative overflow-hidden', props.class)"
  >
    <ScrollAreaViewport class="h-full w-full rounded-[inherit]">
      <slot />
    </ScrollAreaViewport>
    <ScrollAreaScrollbar
      class="flex touch-none select-none transition-colors duration-150 ease-out hover:bg-accent data-[orientation=horizontal]:h-2.5 data-[orientation=vertical]:w-2.5 data-[orientation=horizontal]:flex-col"
      orientation="vertical"
    >
      <ScrollAreaThumb class="relative flex-1 rounded-full bg-border" />
    </ScrollAreaScrollbar>
    <ScrollAreaScrollbar
      class="flex touch-none select-none transition-colors duration-150 ease-out hover:bg-accent data-[orientation=horizontal]:h-2.5 data-[orientation=vertical]:w-2.5 data-[orientation=horizontal]:flex-col"
      orientation="horizontal"
    >
      <ScrollAreaThumb class="relative flex-1 rounded-full bg-border" />
    </ScrollAreaScrollbar>
    <ScrollAreaCorner />
  </ScrollAreaRoot>
</template>
