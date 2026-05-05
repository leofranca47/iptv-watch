<?php

namespace App\Livewire;

use App\Models\Channel;
use App\Models\Setting;
use App\Services\EpgService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Channel $selectedChannel = null;

    public string $search = '';

    public string $selectedGroup = '';

    public string $selectedTab = 'canais';

    public int $channelPage = 1;

    public int $perPage = 12;

    public ?string $lastSync = null;

    public string $m3uUrl = '';

    public string $epgUrl = '';

    public string $statusMessage = '';

    public bool $showSettings = false;

    public bool $isImporting = false;

    public array $importProgress = [
        'current_channel' => '',
        'current_logo' => null,
        'processed' => 0,
        'total' => 0,
        'percentage' => 0,
    ];

    public array $channelPrograms = [];

    protected $listeners = [
        'm3uUrlUpdated' => 'refreshData',
        'channelsSynced' => 'refreshData',
        'pollProgress' => 'pollProgress',
    ];

    public function mount(): void
    {
        $this->lastSync = Setting::getValue('last_sync');
        $this->m3uUrl = Setting::getValue('m3u_url', '');
        $this->epgUrl = Setting::getValue('epg_url', '');

        $lastChannelId = Setting::getValue('last_channel_id');
        if ($lastChannelId) {
            $this->selectedChannel = Channel::find($lastChannelId);
            $this->loadChannelPrograms();
        }

        $this->loadProgressFromSession();
    }

    public function selectChannel(Channel $channel): void
    {
        $this->selectedChannel = $channel;
        Setting::setValue('last_channel_id', (string) $channel->id);
        $this->dispatch('playChannel', $channel->stream_url);
        $this->loadChannelPrograms();
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

    public function saveEpgSettings(): void
    {
        if (empty($this->epgUrl)) {
            $this->statusMessage = 'Configure a URL do EPG primeiro.';

            return;
        }

        try {
            $epgService = new EpgService;
            $xmlContent = $epgService->fetchFromUrl($this->epgUrl);
            $count = $epgService->parse($xmlContent);
            Setting::setValue('epg_url', $this->epgUrl);
            $this->statusMessage = "EPG importado com sucesso! ({$count} programas)";
        } catch (\Exception $e) {
            $this->statusMessage = 'Erro ao importar EPG: '.$e->getMessage();
        }
    }

    public function syncChannels(): void
    {
        $this->dispatch('startSync');
    }

    public function setTab(string $tab): void
    {
        $this->selectedTab = $tab;
        $this->channelPage = 1;
        unset($this->allChannels, $this->groups, $this->totalChannels, $this->hasMoreChannels);
    }

    public function pollProgress(): void
    {
        $this->loadProgressFromSession();
    }

    private function loadProgressFromSession(): void
    {
        $this->importProgress = Session::get('import_progress', [
            'current_channel' => '',
            'current_logo' => null,
            'processed' => 0,
            'total' => 0,
            'percentage' => 0,
        ]);
        $this->isImporting = Session::get('importing', false);
    }

    private function loadChannelPrograms(): void
    {
        if (! $this->selectedChannel) {
            $this->channelPrograms = [];

            return;
        }

        $epgService = new EpgService;
        $this->channelPrograms = $epgService->getChannelPrograms($this->selectedChannel->id);
    }

    public function getAllChannelsProperty()
    {
        $type = $this->selectedTab === 'canais' ? 'channel' : 'movie';

        return Channel::active()
            ->where('type', $type)
            ->search($this->search)
            ->byGroup($this->selectedGroup)
            ->orderBy('name')
            ->skip(($this->channelPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();
    }

    public function getTotalChannelsProperty()
    {
        $type = $this->selectedTab === 'canais' ? 'channel' : 'movie';

        return Channel::active()
            ->where('type', $type)
            ->search($this->search)
            ->byGroup($this->selectedGroup)
            ->count();
    }

    public function getHasMoreChannelsProperty()
    {
        $totalFetched = $this->channelPage * $this->perPage;

        return $totalFetched < $this->totalChannels;
    }

    public function getGroupsProperty()
    {
        $type = $this->selectedTab === 'canais' ? 'channel' : 'movie';

        return Channel::active()
            ->where('type', $type)
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
        $this->channelPrograms = [];
        unset($this->allChannels, $this->groups, $this->totalChannels, $this->hasMoreChannels);
        $this->loadProgressFromSession();
    }

    public function updatedSelectedTab(): void
    {
        $this->channelPage = 1;
        unset($this->allChannels, $this->groups, $this->totalChannels, $this->hasMoreChannels);
    }

    public function updatedSearch(): void
    {
        $this->channelPage = 1;
    }

    public function updatedSelectedGroup(): void
    {
        $this->channelPage = 1;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
