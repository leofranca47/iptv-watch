<div
    x-data="{
        sidebarVisible: true,
        search: '',
        selectedGroup: 'Todos'
    }"
    @sidebar-toggled.window="sidebarVisible = $event.detail"
    @search-updated.window="search = $event.detail"
    @group-updated.window="selectedGroup = $event.detail"
>
    <div
        :class="sidebarVisible ? 'w-64' : 'w-16'"
        class="flex-shrink-0 bg-gray-900 dark:bg-gray-800 text-white transition-all duration-300 flex flex-col"
    >
        <div class="p-4 flex items-center justify-between border-b border-gray-700">
            <span x-show="sidebarVisible" x-transition class="font-semibold">Canais</span>
            <button
                @click="sidebarVisible = !sidebarVisible; $dispatch('sidebarToggled', sidebarVisible)"
                class="p-1 hover:bg-gray-700 rounded transition"
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
            <div class="p-4 space-y-4">
                <input
                    type="text"
                    x-model="search"
                    @input="$dispatch('search-updated', search)"
                    placeholder="Buscar canal..."
                    class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

                <select
                    x-model="selectedGroup"
                    @change="$dispatch('group-updated', selectedGroup)"
                    class="w-full px-3 py-2 bg-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                    <option value="Todos">Todos</option>
                    @foreach($this->groups as $group)
                        <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach
                </select>
            </div>

            <div class="px-2 pb-4">
                @foreach($this->channels as $channel)
                    <button
                        wire:click="selectChannel({{ $channel->id }})"
                        class="w-full flex items-center gap-3 p-3 hover:bg-gray-700 rounded-lg transition text-left"
                    >
                        @if($channel->logo)
                            <img src="{{ $channel->logo }}" alt="" class="w-8 h-8 object-contain rounded bg-gray-700">
                        @else
                            <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded text-xs">
                                {{ substr($channel->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0" x-show="sidebarVisible">
                            <div class="text-sm font-medium truncate">{{ $channel->name }}</div>
                            <div class="text-xs text-gray-400 truncate">{{ $channel->group }}</div>
                        </div>
                    </button>
                @endforeach

                @if($this->channels->isEmpty())
                    <p class="text-center text-gray-400 text-sm py-4">Nenhum canal encontrado</p>
                @endif
            </div>
        </div>

        <div x-show="!sidebarVisible" x-transition class="flex-1 overflow-y-auto py-2">
            @foreach($this->channels as $channel)
                <button
                    wire:click="selectChannel({{ $channel->id }})"
                    class="w-full p-3 hover:bg-gray-700 rounded-lg transition flex justify-center"
                >
                    @if($channel->logo)
                        <img src="{{ $channel->logo }}" alt="" class="w-8 h-8 object-contain rounded bg-gray-700">
                    @else
                        <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded text-xs">
                            {{ substr($channel->name, 0, 1) }}
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>