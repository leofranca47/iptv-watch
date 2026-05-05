<?php

use App\Http\Controllers\Api\StreamProxyController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::post('/sync/start', [SyncController::class, 'start']);
Route::get('/sync/progress', [SyncController::class, 'progress']);

Route::get('/stream/m3u8', [StreamProxyController::class, 'm3u8']);
Route::get('/stream/segment', [StreamProxyController::class, 'segment']);
