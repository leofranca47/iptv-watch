<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class M3UParser
{
    public function fetchFromUrl(string $url): string
    {
        $response = Http::timeout(30)->get($url);

        if ($response->failed()) {
            throw new \Exception('Erro ao acessar URL: '.$response->status());
        }

        return $response->body();
    }

    public function parse(string $content): array
    {
        $channels = [];
        $lines = explode("\n", $content);
        $currentInfo = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#EXTINF')) {
                $currentInfo = $this->parseExtInf($line);
            } elseif (! empty($line) && ! str_starts_with($line, '#') && $currentInfo) {
                $currentInfo['stream_url'] = $line;
                $channels[] = $currentInfo;
                $currentInfo = null;
            }
        }

        return $channels;
    }

    protected function parseExtInf(string $line): array
    {
        $info = [
            'name' => '',
            'logo' => null,
            'group' => 'Outros',
            'stream_url' => '',
        ];

        if (preg_match('/tvg-name="([^"]*)"/', $line, $matches)) {
            $info['name'] = $matches[1];
        }

        if (preg_match('/tvg-logo="([^"]*)"/', $line, $matches)) {
            $info['logo'] = $matches[1];
        }

        if (preg_match('/group-title="([^"]*)"/', $line, $matches)) {
            $info['group'] = $matches[1];
        }

        if (preg_match('/,([^,]*)$/', $line, $matches)) {
            $info['name'] = $matches[1];
        }

        return $info;
    }

    public function syncToDatabase(array $channels): void
    {
        Channel::query()->update(['is_active' => false]);

        foreach ($channels as $channelData) {
            if (empty($channelData['stream_url'])) {
                continue;
            }

            Channel::updateOrCreate(
                ['stream_url' => $channelData['stream_url']],
                [
                    'name' => $channelData['name'] ?: basename($channelData['stream_url']),
                    'logo' => $channelData['logo'] ?? null,
                    'group' => $channelData['group'] ?: 'Outros',
                    'is_active' => true,
                ]
            );
        }

        Setting::setValue('last_sync', now()->toDateTimeString());
    }

    public function syncFromUrl(string $url): void
    {
        $content = $this->fetchFromUrl($url);
        $channels = $this->parse($content);
        $this->syncToDatabase($channels);
    }
}
