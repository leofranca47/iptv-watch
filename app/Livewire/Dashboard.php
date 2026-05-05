<?php

namespace App\Livewire;

use App\Models\Channel;
use App\Models\Setting;
use App\Services\M3UParser;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Channel $selectedChannel = null;

    public string $search = '';

    public string $selectedGroup = 'Todos';

    public ?string $lastSync = null;

    public string $m3uUrl = '';

    public string $statusMessage = '';

    public bool $showSettings = false;

    protected $listeners = [
        'm3uUrlUpdated' => 'refreshData',
        'channelsSynced' => 'refreshData',
    ];

    public function mount(): void
    {
        $this->lastSync = Setting::getValue('last_sync');
        $this->m3uUrl = Setting::getValue('m3u_url', '');

        $lastChannelId = Setting::getValue('last_channel_id');
        if ($lastChannelId) {
            $this->selectedChannel = Channel::find($lastChannelId);
        }
    }

    public function selectChannel(Channel $channel): void
    {
        $this->selectedChannel = $channel;
        Setting::setValue('last_channel_id', (string) $channel->id);
        $this->dispatch('playChannel', $channel->stream_url);
    }

    public function openSettings(): void
    {
        $this->showSettings = true;
    }

    public function closeSettings(): void
    {
        $this->showSettings = false;
        $this->statusMessage = '';
    }

    public function saveSettings(): void
    {
        $this->validate([
            'm3uUrl' => 'required|url',
        ]);

        Setting::setValue('m3u_url', $this->m3uUrl);
        $this->statusMessage = 'URL salva com sucesso!';

        $this->dispatch('m3uUrlUpdated');
    }

    public function syncChannels(): void
    {
        if (empty($this->m3uUrl)) {
            $this->statusMessage = 'Configure a URL do M3U primeiro.';

            return;
        }

        try {
            $parser = new M3UParser;
            $parser->syncFromUrl($this->m3uUrl);
            $this->statusMessage = 'Lista sincronizada com sucesso!';
            $this->dispatch('channelsSynced');
        } catch (\Exception $e) {
            $this->statusMessage = 'Erro: '.$e->getMessage();
        }
    }

    public function getAllChannelsProperty()
    {
        return Channel::active()
            ->search($this->search !== 'Todos' ? $this->search : '')
            ->byGroup($this->selectedGroup)
            ->orderBy('name')
            ->get();
    }

    public function getGroupsProperty()
    {
        return Channel::active()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->pluck('group')
            ->sort()
            ->toArray();
    }

    public function refreshData(): void
    {
        $this->lastSync = Setting::getValue('last_sync');
        $this->selectedChannel = null;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
