<?php

namespace App\Jobs;

use App\Services\M3UParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SyncChannelsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(
        public string $m3uUrl
    ) {}

    public function handle(): void
    {
        Session::put('importing', true);
        Session::forget('import_error');
        Session::put('import_progress', [
            'current_channel' => 'Baixando lista...',
            'current_logo' => null,
            'processed' => 0,
            'total' => 0,
            'percentage' => 0,
        ]);

        try {
            $parser = new M3UParser;
            $content = $parser->fetchFromUrl($this->m3uUrl);
            $channels = $parser->parse($content);

            Session::put('import_progress', [
                'current_channel' => 'Importando canais...',
                'current_logo' => null,
                'processed' => 0,
                'total' => count($channels),
                'percentage' => 0,
            ]);

            $parser->syncToDatabase($channels, function ($progress) {
                Session::put('import_progress', $progress);
            });

            Session::put('importing', false);
            Session::put('import_progress', [
                'current_channel' => 'Finalizado!',
                'current_logo' => null,
                'processed' => 0,
                'total' => 0,
                'percentage' => 100,
            ]);
        } catch (\Exception $e) {
            Log::error('SyncChannelsJob failed: '.$e->getMessage());
            Session::put('importing', false);
            Session::put('import_error', 'Erro na sincronização. Tente novamente.');
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncChannelsJob failed permanently: '.$exception->getMessage());
        Session::put('importing', false);
        Session::put('import_error', 'Erro na sincronização. Tente novamente.');
    }
}
