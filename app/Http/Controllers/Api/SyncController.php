<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncChannelsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SyncController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $m3uUrl = $request->input('m3u_url');

        if (empty($m3uUrl)) {
            return response()->json(['error' => 'URL não fornecida'], 400);
        }

        $isAlreadyRunning = Session::get('importing', false);

        if ($isAlreadyRunning) {
            return response()->json(['error' => 'Sincronização já está em andamento'], 409);
        }

        SyncChannelsJob::dispatch($m3uUrl);

        return response()->json([
            'success' => true,
            'message' => 'Sincronização iniciada!',
        ]);
    }

    public function progress(): JsonResponse
    {
        return response()->json([
            'importing' => Session::get('importing', false),
            'progress' => Session::get('import_progress', [
                'current_channel' => '',
                'current_logo' => null,
                'processed' => 0,
                'total' => 0,
                'percentage' => 0,
            ]),
            'error' => Session::get('import_error'),
        ]);
    }
}
