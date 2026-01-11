<?php

namespace App\Livewire;

use App\Models\Room;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AdminRooms extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->user()?->is_admin) {
            $this->redirectRoute('dashboard');
        }
    }

    public function render(): View
    {
        $rooms = Room::query()
            ->select(['id', 'code', 'started_at', 'ended_at', 'created_at'])
            ->withCount([
                'participants as participants_count' => function ($query) {
                    $query->whereNull('kicked_at');
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin-rooms', [
            'rooms' => $rooms,
        ])->layout('components.layouts.app', ['title' => 'Admin Rooms']);
    }
}
