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
                    <div class="flex gap-1">
                        <button
                            wire:click="setTab('canais')"
                            class="flex-1 px-3 py-2 text-sm rounded-lg transition {{ $selectedTab === 'canais' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}"
                        >
                            Canais
                        </button>
                        <button
                            wire:click="setTab('filmes')"
                            class="flex-1 px-3 py-2 text-sm rounded-lg transition {{ $selectedTab === 'filmes' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}"
                        >
                            Filmes
                        </button>
                    </div>

                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Buscar..."
                        class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >

                    <select
                        wire:model.live="selectedGroup"
                        class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="">Selecione...</option>
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
                        <p class="text-center text-gray-400 text-sm py-4">Nenhum {{ $selectedTab === 'filmes' ? 'filme' : 'canal' }} encontrado</p>
                    @elseif($this->hasMoreChannels)
                        <button
                            wire:click="channelPage = channelPage + 1"
                            class="w-full py-2 text-sm text-blue-400 hover:text-blue-300"
                        >
                            Carregar mais ({{ $this->totalChannels - ($this->channelPage * $this->perPage) }} restantes)
                        </button>
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

            @if($selectedChannel)
                <div class="px-4 pb-2 flex justify-center">
                    <button
                        onclick="window.open('vlc://' + encodeURIComponent('{{ $selectedChannel->stream_url }}'), '_self')"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition inline-flex items-center gap-2 text-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.65z"/>
                        </svg>
                        Abrir no VLC
                    </button>
                </div>
            @endif

            @if($isImporting)
                <div class="p-4 bg-gray-800 border-t border-gray-700" wire:poll.200ms="pollProgress">
                    <div class="flex items-center gap-4 mb-2">
                        @if($importProgress['current_logo'])
                            <img src="{{ $importProgress['current_logo'] }}" class="w-10 h-10 object-contain rounded bg-gray-700">
                        @else
                            <div class="w-10 h-10 flex items-center justify-center bg-gray-700 rounded">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">{{ $importProgress['current_channel'] }}</p>
                            <p class="text-xs text-gray-400">{{ $importProgress['processed'] }} / {{ $importProgress['total'] }} ({{ $importProgress['percentage'] }}%)</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div
                            class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                            style="width: {{ $importProgress['percentage'] }}%"
                        ></div>
                    </div>
                </div>
            @elseif($selectedChannel && count($channelPrograms) > 0)
                <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                        Programação - {{ $selectedChannel->name }}
                    </h3>
                    <div class="space-y-3">
                        @foreach($channelPrograms as $program)
                            <div class="flex gap-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <div class="text-center min-w-[60px]">
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        {{ \Carbon\Carbon::parse($program['start_time'])->format('H:i') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($program['end_time'])->format('H:i') }}
                                    </p>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $program['title'] }}</p>
                                    @if($program['description'])
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($program['description'], 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($selectedChannel)
                <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-gray-800">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>Nenhuma programação disponível para este canal.</p>
                        <p class="text-sm mt-1">Configure o EPG nas configurações para ver a programação.</p>
                    </div>
                </div>
            @else
                <div class="flex-1 overflow-y-auto p-4 bg-white dark:bg-gray-800">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <p>Selecione um canal na barra lateral para assistir.</p>
                        <p class="text-sm mt-1">Use as abas <strong>Canais</strong> e <strong>Filmes</strong> para navegar.</p>
                    </div>
                </div>
            @endif
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
                            onclick="startSync()"
                            id="syncButton"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Sincronizar
                        </button>
                    </div>

                    <hr class="border-gray-300 dark:border-gray-600">

                    <div>
                        <label for="epgUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            URL do EPG (Guia de Programação)
                        </label>
                        <input
                            type="url"
                            id="epgUrl"
                            wire:model="epgUrl"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="https://exemplo.com/epg.xml"
                        >
                    </div>

                    <button
                        wire:click="saveEpgSettings"
                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Importar EPG
                    </button>

                    <div id="syncStatus"></div>

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
    let hlsInstance = null;
    let syncPollInterval = null;
    let isSyncing = false;

    document.addEventListener('livewire:init', () => {
        Livewire.on('playChannel', (url) => {
            initPlayer(url);
        });

        Livewire.on('startSync', () => {
            startSync();
        });

        startSyncPolling();
    });

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

    function openVlc(url) {
        window.location.href = 'vlc://' + url;
    }

    async function startSync() {
        if (isSyncing) return;

        const m3uUrl = document.querySelector('#m3uUrl')?.value;
        if (!m3uUrl) {
            alert('Configure a URL do M3U primeiro.');
            return;
        }

        isSyncing = true;
        const syncButton = document.getElementById('syncButton');
        if (syncButton) {
            syncButton.disabled = true;
            syncButton.innerHTML = '<svg class="w-4 h-4 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Importando...';
        }

        try {
            const response = await fetch('/api/sync/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ m3u_url: m3uUrl })
            });

            const data = await response.json().catch(() => ({ error: 'Resposta inválida do servidor' }));

            if (!response.ok) {
                throw new Error(data.error || `Erro ${response.status}: Erro ao iniciar sincronização`);
            }

            if (data.success) {
                updateSyncStatus('Sincronização iniciada...');
            }
        } catch (error) {
            console.error('Sync error:', error);
            updateSyncStatus('Erro: ' + error.message);
            isSyncing = false;
            if (syncButton) {
                syncButton.disabled = false;
                syncButton.textContent = 'Sincronizar';
            }
        }
    }

    function startSyncPolling() {
        if (syncPollInterval) return;
        syncPollInterval = setInterval(async () => {
            if (!isSyncing) return;

            try {
                const response = await fetch('/api/sync/progress');
                const data = await response.json();

                if (data.progress) {
                    updateProgressBar(data.progress);
                }

                if (data.error) {
                    updateSyncStatus('Erro: ' + data.error);
                    isSyncing = false;
                    stopSync();
                }

                if (!data.importing && data.progress.percentage === 100) {
                    updateSyncStatus('Lista sincronizada com sucesso!');
                    isSyncing = false;
                    stopSync();
                    setTimeout(() => {
                        Livewire.dispatch('channelsSynced');
                    }, 1000);
                }
            } catch (error) {
                console.error('Poll error:', error);
            }
        }, 200);
    }

    function updateProgressBar(progress) {
        const progressBar = document.getElementById('progressBar');
        const progressChannel = document.getElementById('progressChannel');
        const progressCount = document.getElementById('progressCount');

        if (progressBar) {
            progressBar.style.width = progress.percentage + '%';
        }
        if (progressChannel) {
            progressChannel.textContent = progress.current_channel || 'Processando...';
        }
        if (progressCount) {
            progressCount.textContent = `${progress.processed} / ${progress.total} (${progress.percentage}%)`;
        }
    }

    function updateSyncStatus(message) {
        const statusDiv = document.getElementById('syncStatus');
        if (statusDiv) {
            statusDiv.textContent = message;
            statusDiv.className = message.includes('Erro')
                ? 'p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                : 'p-3 rounded-lg bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        }
    }

    function stopSync() {
        const syncButton = document.getElementById('syncButton');
        if (syncButton) {
            syncButton.disabled = false;
            syncButton.textContent = 'Sincronizar';
        }
    }
</script>
