<?php

namespace App\Livewire;

use App\Models\Channel;
use Livewire\Component;

class ChannelList extends Component
{
    public string $search = '';

    public string $selectedGroup = 'Todos';

    public bool $isCollapsed = false;

    protected $listeners = ['refreshChannels' => '$refresh'];

    public function selectChannel(Channel $channel): void
    {
        $this->dispatch('channelSelected', $channel->id);
    }

    public function getChannelsProperty()
    {
        return Channel::active()
            ->search($this->search)
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

    public function toggleCollapse(): void
    {
        $this->isCollapsed = ! $this->isCollapsed;
        $this->dispatch('sidebarToggled', $this->isCollapsed);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('searchUpdated', $this->search);
    }

    public function updatedSelectedGroup(): void
    {
        $this->dispatch('groupUpdated', $this->selectedGroup);
    }

    public function render()
    {
        return view('livewire.channel-list');
    }
}
