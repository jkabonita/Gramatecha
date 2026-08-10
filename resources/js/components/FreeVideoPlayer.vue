<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface Props {
    src: string;
    poster?: string;
    title?: string;
    autoplay?: boolean;
    width?: string;
    height?: string;
}

const props = withDefaults(defineProps<Props>(), {
    autoplay: false,
    width: '100%',
    height: 'auto',
});

// ── Refs ──────────────────────────────────────────────────────────────────────
const videoContainer = ref<HTMLDivElement | null>(null);
const videoId = ref(`video-player-${Math.random().toString(36).substr(2, 9)}`);
const playerInstance = ref<any>(null);
const isScriptLoaded = ref(false);

// ── Load Script ───────────────────────────────────────────────────────────────
const loadScript = (): Promise<void> => {
    return new Promise((resolve, reject) => {
        // Check if script already loaded
        if ((window as any).FreeVideoPlayer) {
            isScriptLoaded.value = true;
            resolve();
            return;
        }

        // Check if script tag already exists
        const existingScript = document.querySelector(
            'script[src*="free.video.player"]'
        );
        
        if (existingScript) {
            existingScript.addEventListener('load', () => {
                isScriptLoaded.value = true;
                resolve();
            });
            return;
        }

        // Create and load script
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/free-video-player@0.9.8/free.video.player.full.min.js';
        script.async = true;
        
        script.onload = () => {
            console.log('[FreeVideoPlayer] Library loaded successfully');
            isScriptLoaded.value = true;
            resolve();
        };
        
        script.onerror = () => {
            console.error('[FreeVideoPlayer] Failed to load library');
            reject(new Error('Failed to load free-video-player library'));
        };
        
        document.head.appendChild(script);
    });
};

// ── Initialize Player ─────────────────────────────────────────────────────────
const initializePlayer = () => {
    if (!isScriptLoaded.value || !videoContainer.value) {
        console.warn('[FreeVideoPlayer] Cannot initialize - script not loaded or container missing');
        return;
    }

    try {
        const FreeVideoPlayer = (window as any).FreeVideoPlayer;
        
        if (!FreeVideoPlayer) {
            console.error('[FreeVideoPlayer] Library not found on window object');
            return;
        }

        // Initialize the player
        playerInstance.value = new FreeVideoPlayer(videoId.value, {
            url: props.src,
            poster: props.poster,
            autoplay: props.autoplay,
            preload: 'metadata', // Only load metadata initially
            controls: true,
            responsive: true,
            fluid: true,
            playbackRates: [0.5, 1, 1.25, 1.5, 2],
            // Optimizations for streaming
            html5: {
                vhs: {
                    overrideNative: true,
                    limitRenditionByPlayerDimensions: true,
                    useDevicePixelRatio: true,
                },
                nativeVideoTracks: false,
                nativeAudioTracks: false,
                nativeTextTracks: false,
            },
        });

        console.log('[FreeVideoPlayer] Player initialized successfully');
        
        // Listen to player events
        playerInstance.value.on('loadstart', () => {
            console.log('[FreeVideoPlayer] Load started');
        });
        
        playerInstance.value.on('loadedmetadata', () => {
            console.log('[FreeVideoPlayer] Metadata loaded');
        });
        
        playerInstance.value.on('canplay', () => {
            console.log('[FreeVideoPlayer] Can play - ready for playback');
        });
        
        playerInstance.value.on('error', (error: any) => {
            console.error('[FreeVideoPlayer] Error:', error);
        });
        
    } catch (error) {
        console.error('[FreeVideoPlayer] Initialization error:', error);
    }
};

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
    try {
        await loadScript();
        // Wait a bit for DOM to be ready
        setTimeout(() => {
            initializePlayer();
        }, 100);
    } catch (error) {
        console.error('[FreeVideoPlayer] Mount error:', error);
    }
});

onUnmounted(() => {
    // Clean up player instance
    if (playerInstance.value && typeof playerInstance.value.dispose === 'function') {
        playerInstance.value.dispose();
        console.log('[FreeVideoPlayer] Player disposed');
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
    <div ref="videoContainer" class="relative w-full bg-black">
        <!-- Video element that FreeVideoPlayer will enhance -->
        <video
            :id="videoId"
            class="video-js vjs-default-skin vjs-big-play-centered w-full"
            :style="{ width: props.width, height: props.height, maxHeight: '80vh' }"
            controls
            preload="metadata"
            playsinline
            crossorigin="anonymous"
        >
            <source :src="props.src" type="video/mp4">
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a
                web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">
                    supports HTML5 video
                </a>
            </p>
        </video>
    </div>
</template>

<style>
/* Ensure video.js styles are applied */
.video-js {
    width: 100% !important;
    height: auto !important;
    max-height: 80vh !important;
}

.video-js .vjs-tech {
    object-fit: contain;
}

/* Loading spinner */
.video-js .vjs-loading-spinner {
    border-color: rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
}

/* Big play button styling */
.video-js .vjs-big-play-button {
    background-color: rgba(0, 0, 0, 0.7);
    border: 2px solid #fff;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    line-height: 76px;
    font-size: 40px;
    left: 50%;
    top: 50%;
    margin-left: -40px;
    margin-top: -40px;
}

.video-js:hover .vjs-big-play-button,
.video-js .vjs-big-play-button:focus {
    background-color: rgba(0, 0, 0, 0.9);
}

/* Control bar */
.video-js .vjs-control-bar {
    background-color: rgba(0, 0, 0, 0.8);
}

/* Progress bar */
.video-js .vjs-play-progress {
    background-color: #3b82f6;
}

.video-js .vjs-load-progress {
    background-color: rgba(255, 255, 255, 0.3);
}
</style>
