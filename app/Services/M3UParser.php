<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class M3UParser
{
    private const MOVIE_PATTERNS = [
        'filme', 'filmes', 'movie', 'movies', 'cinema',
        'serie', 'series', 'série', 'séries',
        'anime', 'desenho', 'documentario', 'documentário',
    ];

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
            'type' => 'channel',
        ];

        if (preg_match('/tvg-name="([^"]*)"/', $line, $matches)) {
            $info['name'] = $matches[1];
        }

        if (preg_match('/tvg-logo="([^"]*)"/', $line, $matches)) {
            $info['logo'] = $matches[1];
        }

        if (preg_match('/group-title="([^"]*)"/', $line, $matches)) {
            $info['group'] = $matches[1];
            $info['type'] = $this->isMovie($matches[1]) ? 'movie' : 'channel';
        }

        if (preg_match('/,([^,]*)$/', $line, $matches)) {
            $info['name'] = $matches[1];
        }

        return $info;
    }

    public function isMovie(string $group): bool
    {
        $groupLower = mb_strtolower($group);

        foreach (self::MOVIE_PATTERNS as $pattern) {
            if (str_contains($groupLower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function syncToDatabase(array $channels, ?callable $progressCallback = null): void
    {
        Channel::query()->update(['is_active' => false]);

        $total = count($channels);
        $processed = 0;
        $chunkSize = 100;

        $filteredChannels = array_filter($channels, fn ($c) => ! empty($c['stream_url']));

        foreach (array_chunk($filteredChannels, $chunkSize) as $chunk) {
            $upsertData = [];
            $lastInChunk = end($chunk);

            foreach ($chunk as $channelData) {
                $upsertData[] = [
                    'stream_url' => $channelData['stream_url'],
                    'name' => $channelData['name'] ?: basename($channelData['stream_url']),
                    'logo' => $channelData['logo'] ?? null,
                    'group' => $channelData['group'] ?: 'Outros',
                    'type' => $channelData['type'] ?? 'channel',
                    'is_active' => true,
                ];
            }

            Channel::upsert(
                $upsertData,
                ['stream_url'],
                ['name', 'logo', 'group', 'type', 'is_active']
            );

            $processed += count($chunk);

            $progress = [
                'current_channel' => $lastInChunk['name'] ?? 'Canal',
                'current_logo' => $lastInChunk['logo'] ?? null,
                'processed' => $processed,
                'total' => $total,
                'percentage' => (int) (($processed / $total) * 100),
            ];

            Session::put('import_progress', $progress);

            if ($progressCallback) {
                $progressCallback($progress);
            }
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
