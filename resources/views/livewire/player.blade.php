<div>
    @if ($status === 'idle')
        <div class="aspect-video bg-gray-800 flex items-center justify-center rounded-lg">
            <div class="text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.65z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Selecione um canal para assistir</p>
            </div>
        </div>
    @elseif ($status === 'loading')
        <div class="aspect-video bg-gray-800 flex items-center justify-center rounded-lg">
            <div class="text-center text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <p>Carregando...</p>
            </div>
        </div>
    @elseif ($status === 'error')
        <div class="aspect-video bg-gray-800 flex items-center justify-center rounded-lg">
            <div class="text-center text-red-400">
                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mb-2">{{ $errorMessage ?: 'Erro ao reproduzir canal' }}</p>
                <button
                    wire:click="retry"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition"
                >
                    Tentar novamente
                </button>
            </div>
        </div>
    @elseif ($status === 'playing' && $streamUrl)
        <div class="relative aspect-video bg-black rounded-lg overflow-hidden group">
            <video
                id="hls-player"
                class="w-full h-full"
                controls
            ></video>
            <button
                onclick="toggleFullscreen()"
                class="absolute top-2 right-2 p-2 bg-black/50 hover:bg-black/70 rounded-lg transition"
                title="Fullscreen"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
            </button>
        </div>
    @endif
</div>

@if ($streamUrl)
    @once
    @endonce
@endif

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('playChannel', (url) => {
        initPlayer(url);
    });
});

let hlsInstance = null;

function initPlayer(url) {
    const video = document.getElementById('hls-player');
    if (!video) return;

    if (hlsInstance) {
        hlsInstance.destroy();
        hlsInstance = null;
    }

    if (Hls.isSupported()) {
        hlsInstance = new Hls();
        hlsInstance.loadSource(url);
        hlsInstance.attachMedia(video);
        hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
            video.play();
        });
        hlsInstance.on(Hls.Events.ERROR, (event, data) => {
            if (data.fatal) {
                Livewire.dispatch('playerError', { message: 'Erro ao reproduzir stream' });
            }
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = url;
        video.play();
    }
}

function toggleFullscreen() {
    const video = document.getElementById('hls-player');
    if (video) {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            video.requestFullscreen();
        }
    }
}
</script>