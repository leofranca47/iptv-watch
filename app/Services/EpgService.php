<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class EpgService
{
    private const EPG_PATTERNS = [
        'Filmes', 'Movies', 'Cinema', 'Series', 'Séries',
    ];

    public function fetchFromUrl(string $url): string
    {
        $response = Http::timeout(60)->get($url);

        if ($response->failed()) {
            throw new \Exception('Erro ao acessar URL do EPG: '.$response->status());
        }

        return $response->body();
    }

    public function parse(string $xmlContent, ?callable $progressCallback = null): int
    {
        $programCount = 0;
        $totalProgrammes = 0;

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \Exception('Erro ao parsear XML EPG: '.($errors[0]->message ?? 'Unknown error'));
        }

        $epgChannels = [];
        foreach ($xml->channel as $channelElement) {
            $channelId = (string) $channelElement['id'];
            $displayName = (string) ($channelElement->{'display-name'} ?? '');
            $epgChannels[$channelId] = $displayName;
        }

        $dbChannels = Channel::active()->where('type', 'channel')->get();

        $channelMapping = $this->buildChannelMapping($dbChannels, $epgChannels);

        $programs = [];
        $programmesByChannel = [];

        foreach ($xml->programme as $programme) {
            $epgChannelId = (string) $programme['channel'];
            if (! isset($programmesByChannel[$epgChannelId])) {
                $programmesByChannel[$epgChannelId] = [];
            }

            $start = (string) $programme['start'];
            $stop = (string) $programme['stop'];
            $title = isset($programme->title) ? (string) $programme->title : 'Sem título';
            $desc = isset($programme->desc) ? (string) $programme->desc : null;

            $programmesByChannel[$epgChannelId][] = [
                'start_time' => $this->parseXmlDate($start),
                'end_time' => $this->parseXmlDate($stop),
                'title' => $title,
                'description' => $desc,
            ];
            $totalProgrammes++;
        }

        Program::truncate();

        foreach ($programmesByChannel as $epgChannelId => $channelProgrammes) {
            if (! isset($channelMapping[$epgChannelId])) {
                continue;
            }

            $dbChannelId = $channelMapping[$epgChannelId];

            foreach ($channelProgrammes as $program) {
                Program::create([
                    'channel_id' => $dbChannelId,
                    'start_time' => $program['start_time'],
                    'end_time' => $program['end_time'],
                    'title' => $program['title'],
                    'description' => $program['description'],
                ]);
                $programCount++;
            }
        }

        return $programCount;
    }

    private function buildChannelMapping(iterable $dbChannels, array $epgChannels): array
    {
        $mapping = [];

        foreach ($dbChannels as $dbChannel) {
            $dbNameNormalized = $this->normalizeChannelName($dbChannel->name);
            $bestMatch = null;
            $bestScore = 0;

            foreach ($epgChannels as $epgId => $epgName) {
                if ($epgId === '0' || empty($epgId)) {
                    continue;
                }

                $epgNameNormalized = $this->normalizeChannelName($epgName);

                $score = $this->calculateMatchScore($dbNameNormalized, $epgNameNormalized, $epgId);

                if ($score > $bestScore && $score >= 0.5) {
                    $bestScore = $score;
                    $bestMatch = $epgId;
                }
            }

            if ($bestMatch) {
                $mapping[$bestMatch] = $dbChannel->id;
            }
        }

        return $mapping;
    }

    private function normalizeChannelName(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/\s+(sd|hd|fhd|h265|4k)\s*$/i', '', $name);
        $name = preg_replace('/\|.*$/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);

        return $name;
    }

    private function calculateMatchScore(string $dbName, string $epgName, string $epgId): float
    {
        if ($dbName === $epgName) {
            return 1.0;
        }

        if (str_contains($dbName, $epgName) || str_contains($epgName, $dbName)) {
            return 0.9;
        }

        $dbParts = explode(' ', $dbName);
        $epgParts = explode(' ', $epgName);
        $commonParts = array_intersect($dbParts, $epgParts);

        if (count($commonParts) >= 2) {
            return 0.7;
        }

        $epgIdBase = preg_replace('/\.(br|com)?$/i', '', $epgId);
        $epgIdBase = str_replace('.', ' ', $epgIdBase);

        if (str_contains(mb_strtolower($dbName), mb_strtolower($epgIdBase))) {
            return 0.6;
        }

        return 0;
    }

    private function parseXmlDate(string $date): Carbon
    {
        $date = preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1-$2-$3 $4:$5:$6', $date);

        return Carbon::parse($date);
    }

    public function getChannelPrograms(int $channelId): array
    {
        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay()->endOfDay();

        return Program::where('channel_id', $channelId)
            ->where('start_time', '<=', $tomorrow)
            ->where('end_time', '>=', $now->copy()->subHour())
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }
}
