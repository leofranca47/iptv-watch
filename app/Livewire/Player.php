<?php

namespace App\Livewire;

use Livewire\Component;

class Player extends Component
{
    public ?string $streamUrl = null;

    public string $status = 'idle';

    public string $errorMessage = '';

    protected $listeners = [
        'playChannel' => 'play',
    ];

    public function play(string $url): void
    {
        $this->streamUrl = $url;
        $this->status = 'loading';
        $this->errorMessage = '';
    }

    public function onPlaying(): void
    {
        $this->status = 'playing';
    }

    public function onError(string $message): void
    {
        $this->status = 'error';
        $this->errorMessage = $message;
    }

    public function retry(): void
    {
        if ($this->streamUrl) {
            $this->play($this->streamUrl);
        }
    }

    public function render()
    {
        return view('livewire.player');
    }
}
