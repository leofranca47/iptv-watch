<div>
    @if ($showSettings)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Configurações</h2>
                    <button wire:click="close" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
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
                            wire:click="save"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Salvar
                        </button>
                        <button
                            wire:click="sync"
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