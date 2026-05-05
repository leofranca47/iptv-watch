<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\M3UParser;
use Livewire\Component;

class Settings extends Component
{
    public string $m3uUrl = '';

    public string $statusMessage = '';

    public bool $showSettings = false;

    protected $listeners = ['openSettings' => 'open'];

    public function mount(): void
    {
        $this->m3uUrl = Setting::getValue('m3u_url', '');
    }

    public function open(): void
    {
        $this->showSettings = true;
    }

    public function close(): void
    {
        $this->showSettings = false;
        $this->statusMessage = '';
    }

    public function save(): void
    {
        $this->validate([
            'm3uUrl' => 'required|url',
        ]);

        Setting::setValue('m3u_url', $this->m3uUrl);
        $this->statusMessage = 'URL salva com sucesso!';

        $this->dispatch('m3uUrlUpdated');
    }

    public function sync(): void
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

    public function render()
    {
        return view('livewire.settings');
    }
}
