<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\ChatInterface;

Route::get('/', ChatInterface::class);

// Authentication Routes
Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
    Route::get('/routers', \App\Livewire\Admin\ManageRouters::class)->name('admin.routers');
    Route::get('/providers', \App\Livewire\Admin\ManageProviders::class)->name('admin.providers');
});
