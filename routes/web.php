<?php

use App\Livewire\Dashboard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->withoutMiddleware([VerifyCsrfToken::class]);
