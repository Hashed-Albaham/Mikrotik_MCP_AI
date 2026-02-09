<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\ChatInterface;

Route::get('/', ChatInterface::class);

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class);
    Route::get('/routers', \App\Livewire\Admin\ManageRouters::class);
    Route::get('/providers', \App\Livewire\Admin\ManageProviders::class);
});
