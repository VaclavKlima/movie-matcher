<?php

namespace App\Livewire;

use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminTrends extends Component
{
    public function mount(): void
    {
        if (! auth()->user()?->is_admin) {
            $this->redirectRoute('dashboard');
            return;
        }
    }

    public function render(): View
    {
        $stats = Cache::remember('admin.trends.v1', now()->addMinutes(10), function () {
            $totalRooms = Room::count();
            $endedRooms = Room::whereNotNull('ended_at')->count();
            $activeRooms = Room::whereNotNull('started_at')
                ->whereNull('ended_at')
                ->count();
            $lobbyRooms = Room::whereNull('started_at')
                ->whereNull('ended_at')
                ->count();
            $matchedRooms = Room::whereNotNull('matched_movie_id')->count();
            $lastWeekRooms = Room::whereNotNull('ended_at')
                ->where('ended_at', '>=', now()->subDays(7))
                ->count();

            $topMovies = DB::table('rooms')
                ->join('movies', 'movies.id', '=', 'rooms.matched_movie_id')
                ->whereNotNull('rooms.ended_at')
                ->select('movies.id', 'movies.name', DB::raw('COUNT(*) as total'))
                ->groupBy('movies.id', 'movies.name')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $topGenres = DB::table('rooms')
                ->join('movie_genre', 'movie_genre.movie_id', '=', 'rooms.matched_movie_id')
                ->join('genres', 'genres.id', '=', 'movie_genre.genre_id')
                ->whereNotNull('rooms.ended_at')
                ->select('genres.id', 'genres.name', DB::raw('COUNT(*) as total'))
                ->groupBy('genres.id', 'genres.name')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $topActors = DB::table('rooms')
                ->join('movie_actor', 'movie_actor.movie_id', '=', 'rooms.matched_movie_id')
                ->join('actors', 'actors.id', '=', 'movie_actor.actor_id')
                ->whereNotNull('rooms.ended_at')
                ->select('actors.id', 'actors.name', DB::raw('COUNT(*) as total'))
                ->groupBy('actors.id', 'actors.name')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $matchesByDay = DB::table('rooms')
                ->whereNotNull('ended_at')
                ->whereNotNull('matched_movie_id')
                ->where('ended_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(ended_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->map(function ($row) {
                    return [
                        'day' => Carbon::parse($row->day)->format('M j'),
                        'total' => (int) $row->total,
                    ];
                });

            return [
                'total_rooms' => $totalRooms,
                'ended_rooms' => $endedRooms,
                'active_rooms' => $activeRooms,
                'lobby_rooms' => $lobbyRooms,
                'matched_rooms' => $matchedRooms,
                'last_week_rooms' => $lastWeekRooms,
                'top_movies' => $topMovies,
                'top_genres' => $topGenres,
                'top_actors' => $topActors,
                'matches_by_day' => $matchesByDay,
            ];
        });

        return view('livewire.admin-trends', [
            'stats' => $stats,
        ])->layout('components.layouts.app', ['title' => 'Trends Dashboard']);
    }
}
