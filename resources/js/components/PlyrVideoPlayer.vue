<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';

interface Props {
    src: string;
    poster?: string;
    title?: string;
    autoplay?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    autoplay: false,
});

// ── Refs ──────────────────────────────────────────────────────────────────────
const videoElement = ref<HTMLVideoElement | null>(null);
const playerInstance = ref<any>(null);
const isScriptLoaded = ref(false);
const isCssLoaded = ref(false);

// ── Load Plyr Assets ──────────────────────────────────────────────────────────
const loadPlyrAssets = (): Promise<void> => {
    return new Promise((resolve) => {
        const promises: Promise<void>[] = [];

        // Load CSS
        if (!document.querySelector('link[href*="plyr"]')) {
            const cssPromise = new Promise<void>((resolveCSS) => {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/plyr@3.8.4/dist/plyr.min.css';
                link.onload = () => {
                    console.log('[Plyr] CSS loaded');
                    isCssLoaded.value = true;
                    resolveCSS();
                };
                document.head.appendChild(link);
            });
            promises.push(cssPromise);
        } else {
            isCssLoaded.value = true;
        }

        // Load JavaScript
        if (!(window as any).Plyr) {
            const scriptPromise = new Promise<void>((resolveScript) => {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/plyr@3.8.4/dist/plyr.min.js';
                script.async = true;
                script.onload = () => {
                    console.log('[Plyr] Script loaded');
                    isScriptLoaded.value = true;
                    resolveScript();
                };
                document.head.appendChild(script);
            });
            promises.push(scriptPromise);
        } else {
            isScriptLoaded.value = true;
        }

        Promise.all(promises).then(() => resolve());
    });
};

// ── Initialize Plyr ───────────────────────────────────────────────────────────
const initializePlyr = () => {
    if (!videoElement.value || !(window as any).Plyr) {
        console.warn('[Plyr] Cannot initialize - element or library missing');
        return;
    }

    try {
        const Plyr = (window as any).Plyr;

        // Initialize Plyr with optimized settings
        playerInstance.value = new Plyr(videoElement.value, {
            // Controls to show
            controls: [
                'play-large',
                'play',
                'progress',
                'current-time',
                'duration',
                'mute',
                'volume',
                'settings',
                'pip',
                'airplay',
                'fullscreen',
            ],
            
            // Settings menu
            settings: ['quality', 'speed'],
            
            // Playback speed options
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 2],
            },
            
            // Quality options (if available)
            quality: {
                default: 576,
                options: [4320, 2880, 2160, 1440, 1080, 720, 576, 480, 360, 240],
            },
            
            // Enable keyboard shortcuts
            keyboard: { focused: true, global: false },
            
            // Tooltip on controls
            tooltips: { controls: true, seek: true },
            
            // Auto-hide controls
            hideControls: true,
            
            // Click to play/pause toggle
            clickToPlay: true,
            
            // Enable captions/subtitles if available
            captions: { active: true, language: 'auto', update: true },
            
            // CRITICAL: Use metadata preload for fast initial load
            preload: 'metadata',
            
            // Autoplay setting
            autoplay: props.autoplay,
            
            // Other settings
            clickToPlay: true,
            disableContextMenu: false,
            resetOnEnd: false,
            ratio: '16:9',
            
            // Storage for user preferences
            storage: { enabled: true, key: 'plyr' },
        });

        console.log('[Plyr] Player initialized successfully');

        // Event listeners
        playerInstance.value.on('ready', () => {
            console.log('[Plyr] Ready');
        });

        playerInstance.value.on('loadstart', () => {
            console.log('[Plyr] Load started - fetching metadata');
        });

        playerInstance.value.on('loadedmetadata', () => {
            console.log('[Plyr] Metadata loaded - duration:', playerInstance.value.duration);
        });

        playerInstance.value.on('canplay', () => {
            console.log('[Plyr] Can play - ready for playback');
        });

        playerInstance.value.on('playing', () => {
            console.log('[Plyr] Playing');
        });

        playerInstance.value.on('waiting', () => {
            console.log('[Plyr] Waiting for data...');
        });

        playerInstance.value.on('error', (event: any) => {
            console.error('[Plyr] Error:', event);
        });

    } catch (error) {
        console.error('[Plyr] Initialization error:', error);
    }
};

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
    try {
        await loadPlyrAssets();
        // Small delay to ensure DOM is ready
        setTimeout(() => {
            initializePlyr();
        }, 100);
    } catch (error) {
        console.error('[Plyr] Mount error:', error);
    }
});

onUnmounted(() => {
    if (playerInstance.value) {
        try {
            playerInstance.value.destroy();
            console.log('[Plyr] Player destroyed');
        } catch (error) {
            console.error('[Plyr] Destroy error:', error);
        }
    }
});

// Watch for src changes
watch(() => props.src, (newSrc) => {
    if (playerInstance.value && videoElement.value) {
        videoElement.value.src = newSrc;
        playerInstance.value.source = {
            type: 'video',
            sources: [{
                src: newSrc,
                type: 'video/mp4',
            }],
        };
    }
});

// Expose methods for parent component
defineExpose({
    play: () => playerInstance.value?.play(),
    pause: () => playerInstance.value?.pause(),
    player: playerInstance,
});
</script>

<template>
    <div class="plyr-container w-full bg-black">
        <!-- 
            CRITICAL OPTIMIZATION:
            - preload="metadata" loads only first ~10 seconds
            - Prevents 7-10 second delay from full download
            - Browser fetches additional chunks on-demand during playback
        -->
        <video
            ref="videoElement"
            :poster="props.poster"
            playsinline
            preload="metadata"
            crossorigin="anonymous"
            class="w-full"
            style="max-height: 80vh;"
        >
            <source :src="props.src" type="video/mp4">
            <!-- Fallback for browsers that don't support the video tag -->
            Your browser does not support HTML5 video.
        </video>
    </div>
</template>

<style scoped>
.plyr-container {
    position: relative;
    width: 100%;
}

/* Ensure video fits container properly */
.plyr-container :deep(.plyr) {
    width: 100%;
    max-height: 80vh;
}

.plyr-container :deep(.plyr__video-wrapper) {
    background: #000;
}

/* Custom Plyr theme adjustments */
.plyr-container :deep(.plyr--video) {
    border-radius: 0;
}

.plyr-container :deep(.plyr__control--overlaid) {
    background: rgba(59, 130, 246, 0.9);
}

.plyr-container :deep(.plyr__control--overlaid:hover) {
    background: rgba(59, 130, 246, 1);
}

.plyr-container :deep(.plyr__control--overlaid):focus {
    background: rgba(59, 130, 246, 1);
}

/* Progress bar color */
.plyr-container :deep(.plyr__progress__buffer) {
    color: rgba(255, 255, 255, 0.3);
}

.plyr-container :deep(.plyr__progress input[type="range"]::-webkit-slider-thumb) {
    background: #3b82f6;
}

.plyr-container :deep(.plyr__progress input[type="range"]::-moz-range-thumb) {
    background: #3b82f6;
}

.plyr-container :deep(.plyr__progress input[type="range"]::-ms-thumb) {
    background: #3b82f6;
}

/* Volume slider */
.plyr-container :deep(.plyr__volume input[type="range"]::-webkit-slider-thumb) {
    background: #3b82f6;
}

.plyr-container :deep(.plyr__volume input[type="range"]::-moz-range-thumb) {
    background: #3b82f6;
}

/* Loading spinner color */
.plyr-container :deep(.plyr__spinner) {
    border-color: rgba(255, 255, 255, 0.3);
    border-top-color: #3b82f6;
}
</style>
