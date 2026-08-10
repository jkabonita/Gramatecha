<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Loader2, Play, Pause, Volume2, VolumeX, Maximize, Minimize } from '@lucide/vue';

interface Props {
    src: string;
    poster?: string;
    title?: string;
    autoplay?: boolean;
    mimeType?: string; // Optional MIME type override
}

const props = withDefaults(defineProps<Props>(), {
    autoplay: false,
    mimeType: 'video/mp4',
});

// ── Refs ──────────────────────────────────────────────────────────────────────
const videoElement = ref<HTMLVideoElement | null>(null);
const containerElement = ref<HTMLDivElement | null>(null);
const isLoading = ref(true);
const isPlaying = ref(false);
const isMuted = ref(false);
const isFullscreen = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const bufferedPercent = ref(0);
const volume = ref(1);
const showControls = ref(true);
const controlsTimeout = ref<number | null>(null);

// ── Computed ──────────────────────────────────────────────────────────────────
const progressPercent = computed(() => {
    return duration.value > 0 ? (currentTime.value / duration.value) * 100 : 0;
});

const formattedCurrentTime = computed(() => formatTime(currentTime.value));
const formattedDuration = computed(() => formatTime(duration.value));

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

// ── Video Event Handlers ──────────────────────────────────────────────────────
const handleLoadStart = () => {
    console.log('[Video] Load started');
    isLoading.value = true;
};

const handleCanPlay = () => {
    console.log('[Video] Can play - initial data loaded');
    isLoading.value = false;
};

const handleLoadedMetadata = () => {
    if (videoElement.value) {
        duration.value = videoElement.value.duration;
        console.log(`[Video] Metadata loaded - Duration: ${duration.value.toFixed(2)}s`);
    }
};

const handleTimeUpdate = () => {
    if (videoElement.value) {
        currentTime.value = videoElement.value.currentTime;
    }
};

const handleProgress = () => {
    if (videoElement.value && videoElement.value.buffered.length > 0) {
        const buffered = videoElement.value.buffered.end(videoElement.value.buffered.length - 1);
        const total = videoElement.value.duration;
        bufferedPercent.value = (buffered / total) * 100;
        
        // Log buffer progress for first 10 seconds
        if (buffered < 15) {
            console.log(`[Video Buffer] ${bufferedPercent.value.toFixed(1)}% - ${buffered.toFixed(1)}s loaded`);
        }
    }
};

const handlePlay = () => {
    isPlaying.value = true;
    console.log('[Video] Playing');
};

const handlePause = () => {
    isPlaying.value = false;
    console.log('[Video] Paused');
};

const handleWaiting = () => {
    isLoading.value = true;
    console.log('[Video] Waiting for data...');
};

const handlePlaying = () => {
    isLoading.value = false;
    console.log('[Video] Playing smoothly');
};

const handleError = (event: Event) => {
    console.error('[Video Error]', event);
    isLoading.value = false;
};

// ── Playback Controls ─────────────────────────────────────────────────────────
const togglePlayPause = () => {
    if (!videoElement.value) return;
    
    if (videoElement.value.paused) {
        videoElement.value.play();
    } else {
        videoElement.value.pause();
    }
};

const toggleMute = () => {
    if (!videoElement.value) return;
    videoElement.value.muted = !videoElement.value.muted;
    isMuted.value = videoElement.value.muted;
};

const seek = (event: MouseEvent) => {
    if (!videoElement.value) return;
    
    const progressBar = event.currentTarget as HTMLElement;
    const rect = progressBar.getBoundingClientRect();
    const percent = (event.clientX - rect.left) / rect.width;
    videoElement.value.currentTime = percent * duration.value;
};

const changeVolume = (event: Event) => {
    if (!videoElement.value) return;
    
    const target = event.target as HTMLInputElement;
    const newVolume = parseFloat(target.value);
    videoElement.value.volume = newVolume;
    volume.value = newVolume;
    isMuted.value = newVolume === 0;
};

const toggleFullscreen = async () => {
    if (!containerElement.value) return;
    
    try {
        if (!document.fullscreenElement) {
            await containerElement.value.requestFullscreen();
            isFullscreen.value = true;
        } else {
            await document.exitFullscreen();
            isFullscreen.value = false;
        }
    } catch (error) {
        console.error('[Fullscreen Error]', error);
    }
};

// ── Controls Visibility ───────────────────────────────────────────────────────
const showControlsTemporarily = () => {
    showControls.value = true;
    
    if (controlsTimeout.value) {
        clearTimeout(controlsTimeout.value);
    }
    
    controlsTimeout.value = window.setTimeout(() => {
        if (isPlaying.value) {
            showControls.value = false;
        }
    }, 3000);
};

const handleMouseMove = () => {
    showControlsTemporarily();
};

const handleMouseLeave = () => {
    if (isPlaying.value) {
        showControls.value = false;
    }
};

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => {
    if (videoElement.value) {
        // CRITICAL: Use 'metadata' preload to only load first ~10 seconds
        // This prevents the browser from aggressively downloading the entire file
        // which causes 7-10 second delays on large videos
        videoElement.value.preload = 'metadata';
        
        // The browser will request only minimal data initially
        // Additional chunks are fetched on-demand as the user plays/seeks
        videoElement.value.load();
        
        console.log('[Video Init] Using metadata preload - only loading first 10 seconds');
        
        // Listen for fullscreen changes
        document.addEventListener('fullscreenchange', () => {
            isFullscreen.value = !!document.fullscreenElement;
        });
    }
});

onUnmounted(() => {
    if (controlsTimeout.value) {
        clearTimeout(controlsTimeout.value);
    }
});

// Expose for parent component if needed
defineExpose({
    play: () => videoElement.value?.play(),
    pause: () => videoElement.value?.pause(),
    videoElement,
});
</script>

<template>
    <div 
        ref="containerElement"
        class="relative bg-black w-full"
        @mousemove="handleMouseMove"
        @mouseleave="handleMouseLeave"
    >
        <!-- Video Element with optimized preload settings -->
        <!-- preload="metadata" only loads ~10 seconds of video initially -->
        <!-- This prevents the 7-10 second delay from aggressive downloading -->
        <!-- Using <source> tag for better browser compatibility -->
        <video
            ref="videoElement"
            :poster="props.poster"
            :autoplay="props.autoplay"
            controls
            playsinline
            preload="metadata"
            class="w-full h-full"
            style="max-height: 80vh;"
            crossorigin="anonymous"
            @loadstart="handleLoadStart"
            @loadedmetadata="handleLoadedMetadata"
            @canplay="handleCanPlay"
            @timeupdate="handleTimeUpdate"
            @progress="handleProgress"
            @play="handlePlay"
            @pause="handlePause"
            @waiting="handleWaiting"
            @playing="handlePlaying"
            @error="handleError"
            @click="togglePlayPause"
        >
            <source :src="props.src" :type="props.mimeType">
            Your browser does not support the video tag.
        </video>
        <!-- Loading Spinner -->
        <div 
            v-if="isLoading"
            class="absolute inset-0 flex items-center justify-center bg-black/30 pointer-events-none"
        >
            <Loader2 class="h-12 w-12 text-white animate-spin" />
        </div>

        <!-- Custom Controls -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div 
                v-show="showControls || !isPlaying"
                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-4 pt-20"
            >
                <!-- Progress Bar -->
                <div class="mb-3">
                    <div 
                        class="relative h-1 bg-white/30 rounded-full cursor-pointer group"
                        @click="seek"
                    >
                        <!-- Buffered Progress -->
                        <div 
                            class="absolute inset-y-0 left-0 bg-white/50 rounded-full transition-all"
                            :style="{ width: `${bufferedPercent}%` }"
                        />
                        <!-- Play Progress -->
                        <div 
                            class="absolute inset-y-0 left-0 bg-blue-500 rounded-full transition-all"
                            :style="{ width: `${progressPercent}%` }"
                        />
                        <!-- Hover Handle -->
                        <div 
                            class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                            :style="{ left: `calc(${progressPercent}% - 6px)` }"
                        />
                    </div>
                </div>

                <!-- Control Buttons -->
                <div class="flex items-center gap-3 text-white">
                    <!-- Play/Pause -->
                    <button 
                        @click="togglePlayPause"
                        class="hover:text-blue-400 transition-colors p-1"
                        :aria-label="isPlaying ? 'Pause' : 'Play'"
                    >
                        <Pause v-if="isPlaying" class="h-6 w-6" />
                        <Play v-else class="h-6 w-6" />
                    </button>

                    <!-- Time Display -->
                    <div class="text-sm font-medium tabular-nums">
                        {{ formattedCurrentTime }} / {{ formattedDuration }}
                    </div>

                    <div class="flex-1" />

                    <!-- Volume Control -->
                    <div class="flex items-center gap-2 group">
                        <button 
                            @click="toggleMute"
                            class="hover:text-blue-400 transition-colors p-1"
                            :aria-label="isMuted ? 'Unmute' : 'Mute'"
                        >
                            <VolumeX v-if="isMuted || volume === 0" class="h-5 w-5" />
                            <Volume2 v-else class="h-5 w-5" />
                        </button>
                        <input
                            type="range"
                            min="0"
                            max="1"
                            step="0.1"
                            :value="volume"
                            @input="changeVolume"
                            class="w-0 group-hover:w-20 transition-all duration-300 h-1 bg-white/30 rounded-full appearance-none cursor-pointer [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white"
                            aria-label="Volume"
                        />
                    </div>

                    <!-- Fullscreen -->
                    <button 
                        @click="toggleFullscreen"
                        class="hover:text-blue-400 transition-colors p-1"
                        :aria-label="isFullscreen ? 'Exit fullscreen' : 'Fullscreen'"
                    >
                        <Minimize v-if="isFullscreen" class="h-5 w-5" />
                        <Maximize v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Initial Play Button Overlay -->
        <div 
            v-if="!isPlaying && currentTime === 0 && !isLoading"
            class="absolute inset-0 flex items-center justify-center bg-black/20 cursor-pointer"
            @click="togglePlayPause"
        >
            <div class="w-20 h-20 rounded-full bg-white/90 flex items-center justify-center hover:bg-white transition-colors shadow-2xl">
                <Play class="h-10 w-10 text-black ml-1" />
            </div>
        </div>
    </div>
</template>
