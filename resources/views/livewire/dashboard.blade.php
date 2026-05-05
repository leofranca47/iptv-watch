<div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
    <header class="bg-gray-900 dark:bg-gray-800 text-white px-4 py-3 flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
            </svg>
            <h1 class="text-xl font-semibold">IPTV Watch</h1>
        </div>
        <button
            wire:click="openSettings"
            class="p-2 hover:bg-gray-700 rounded-lg transition"
            title="Configurações"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <div
            x-data="{ sidebarVisible: true }"
            :class="sidebarVisible ? 'w-72' : 'w-16'"
            class="bg-gray-900 dark:bg-gray-800 text-white transition-all duration-300 flex flex-col"
        >
            <div class="p-3 flex items-center justify-between border-b border-gray-700">
                <span x-show="sidebarVisible" x-transition class="font-semibold text-sm">Canais</span>
                <button
                    @click="sidebarVisible = !sidebarVisible"
                    class="p-1.5 hover:bg-gray-700 rounded transition"
                >
                    <svg x-show="sidebarVisible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/>
                    </svg>
                    <svg x-show="!sidebarVisible" x-transition class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <div x-show="sidebarVisible" x-transition class="flex-1 overflow-y-auto">
                <div class="p-3 space-y-2">
                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Buscar canal..."
                        class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >

                    <select
                        wire:model.live="selectedGroup"
                        class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="Todos">Todos</option>
                        @foreach($this->groups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="px-2 pb-3 space-y-1">
                    @foreach($this->allChannels as $channel)
                        <button
                            wire:click="selectChannel({{ $channel->id }})"
                            class="w-full flex items-center gap-3 p-2 hover:bg-gray-700 rounded-lg transition text-left {{ $selectedChannel?->id === $channel->id ? 'bg-gray-700' : '' }}"
                        >
                            @if($channel->logo)
                                <img src="{{ $channel->logo }}" alt="" class="w-8 h-8 object-contain rounded bg-gray-700">
                            @else
                                <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded text-xs font-medium">
                                    {{ strtoupper(substr($channel->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0" x-show="sidebarVisible">
                                <div class="text-sm font-medium truncate">{{ $channel->name }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $channel->group }}</div>
                            </div>
                        </button>
                    @endforeach

                    @if($this->allChannels->isEmpty())
                        <p class="text-center text-gray-400 text-sm py-4">Nenhum canal encontrado</p>
                    @endif
                </div>
            </div>

            <div x-show="!sidebarVisible" x-transition class="flex-1 overflow-y-auto py-2">
                @foreach($this->allChannels as $channel)
                    <button
                        wire:click="selectChannel({{ $channel->id }})"
                        class="w-full p-2 hover:bg-gray-700 rounded-lg transition flex justify-center {{ $selectedChannel?->id === $channel->id ? 'bg-gray-700' : '' }}"
                    >
                        @if($channel->logo)
                            <img src="{{ $channel->logo }}" alt="" class="w-8 h-8 object-contain rounded bg-gray-700">
                        @else
                            <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded text-xs font-medium">
                                {{ strtoupper(substr($channel->name, 0, 1)) }}
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <main class="flex-1 flex flex-col overflow-hidden">
            <div class="p-4 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Buscar canal..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                    <select
                        wire:model.live="selectedGroup"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="Todos">Todos os grupos</option>
                        @foreach($this->groups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                @if($lastSync)
                    <p class="text-xs text-gray-500 mt-2">Última sincronização: {{ $lastSync }}</p>
                @endif
            </div>

            <div class="p-4 flex-shrink-0">
                <div class="relative aspect-video bg-black rounded-lg overflow-hidden group">
                    @if($selectedChannel)
                        <video
                            id="player"
                            class="w-full h-full"
                            controls
                        ></video>
                        <button
                            @click="document.getElementById('player')?.requestFullscreen()"
                            class="absolute top-2 right-2 p-2 bg-black/50 hover:bg-black/70 rounded-lg transition"
                            title="Fullscreen"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                        </button>
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.65z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>Selecione um canal para assistir</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Todos os Canais</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach($this->allChannels as $channel)
                        <button
                            wire:click="selectChannel({{ $channel->id }})"
                            class="flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition {{ $selectedChannel?->id === $channel->id ? 'ring-2 ring-blue-500' : '' }}"
                        >
                            @if($channel->logo)
                                <img src="{{ $channel->logo }}" alt="{{ $channel->name }}" class="w-16 h-16 object-contain mb-2">
                            @else
                                <div class="w-16 h-16 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-lg mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ strtoupper(substr($channel->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-sm font-medium text-center truncate w-full text-gray-900 dark:text-white">{{ $channel->name }}</span>
                            <span class="text-xs text-gray-500">{{ $channel->group }}</span>
                        </button>
                    @endforeach
                </div>

                @if($this->allChannels->isEmpty())
                    <div class="text-center text-gray-500 py-12">
                        <p class="mb-2">Nenhum canal encontrado.</p>
                        <button
                            wire:click="openSettings"
                            class="text-blue-500 hover:underline"
                        >
                            Configure a URL do M3U nas configurações
                        </button>
                    </div>
                @endif
            </div>
        </main>
    </div>

    @if ($showSettings)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Configurações</h2>
                    <button wire:click="closeSettings" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="m3uUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL do M3U
                        </label>
                        <input
                            type="url"
                            id="m3uUrl"
                            wire:model="m3uUrl"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="https://exemplo.com/lista.m3u"
                        >
                        @error('m3uUrl')
                            <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button
                            wire:click="saveSettings"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Salvar
                        </button>
                        <button
                            wire:click="syncChannels"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Sincronizar
                        </button>
                    </div>

                    @if ($statusMessage)
                        <div class="p-3 rounded-lg {{ str_contains($statusMessage, 'Erro') ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                            {{ $statusMessage }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('playChannel', (url) => {
            initPlayer(url);
        });
    });

    let hlsInstance = null;

    function initPlayer(url) {
        const video = document.getElementById('player');
        if (!video) return;

        if (hlsInstance) {
            hlsInstance.destroy();
            hlsInstance = null;
        }

        if (url.includes('.m3u8')) {
            if (Hls.isSupported()) {
                hlsInstance = new Hls();
                hlsInstance.loadSource(url);
                hlsInstance.attachMedia(video);
                hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                video.play();
            }
        } else {
            video.src = url;
            video.play();
        }
    }
</script>