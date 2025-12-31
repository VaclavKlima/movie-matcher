<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\Home;
use App\Livewire\RoomJoin;
use App\Livewire\RoomLobby;
use App\Livewire\RoomMatch;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', Home::class)->name('home');
Route::get('/rooms/create', RoomLobby::class)->name('rooms.create');
Route::get('/rooms/{code}', RoomLobby::class)->name('rooms.show');
Route::get('/rooms/{code}/join', RoomJoin::class)->name('rooms.join');
Route::get('/rooms/{code}/match', RoomMatch::class)->name('rooms.match');

Route::get('dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
