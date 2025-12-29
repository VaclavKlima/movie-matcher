<?php

namespace App\Actions;

use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Facades\DB;

class SuggestMovie
{
    public function execute(int $roomId, int $participantId): ?int
    {
        $seenMovieIds = MovieVote::where('room_id', $roomId)
            ->where('room_participant_id', $participantId)
            ->pluck('movie_id')
            ->all();

        $likedMovieIds = MovieVote::where('room_id', $roomId)
            ->where('decision', 'up')
            ->pluck('movie_id')
            ->all();

        $topGenreIds = DB::table('movie_genre')
            ->select('genre_id', DB::raw('count(*) as total'))
            ->whereIn('movie_id', $likedMovieIds)
            ->groupBy('genre_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('genre_id')
            ->all();

        $avgYear = null;
        if (! empty($likedMovieIds)) {
            $avgYear = Movie::whereIn('id', $likedMovieIds)->avg('year');
            $avgYear = $avgYear ? (int) round($avgYear) : null;
        }

        $yearLower = $avgYear ? $avgYear - 5 : null;
        $yearUpper = $avgYear ? $avgYear + 5 : null;
        $hasGenreTaste = ! empty($topGenreIds);
        $hasYearTaste = $yearLower !== null;
        $hasTaste = $hasGenreTaste || $hasYearTaste;

        $weights = [
            'room_likes' => 3,
            'genre_match' => 2,
            'year_match' => 1,
        ];

        $genreMatchExpr = $hasGenreTaste ? 'count(distinct genre_match.genre_id)' : '0';
        $yearMatchExpr = $hasYearTaste
            ? "case when movies.year between {$yearLower} and {$yearUpper} then 1 else 0 end"
            : '0';

        $candidates = Movie::query()
            ->when(! empty($seenMovieIds), fn ($query) => $query->whereNotIn('movies.id', $seenMovieIds))
            ->when($hasTaste, function ($query) use ($topGenreIds, $yearLower, $yearUpper, $hasGenreTaste, $hasYearTaste) {
                $query->where(function ($innerQuery) use ($topGenreIds, $yearLower, $yearUpper, $hasGenreTaste, $hasYearTaste) {
                    if ($hasGenreTaste) {
                        $innerQuery->whereExists(function ($subquery) use ($topGenreIds) {
                            $subquery->select(DB::raw(1))
                                ->from('movie_genre')
                                ->whereColumn('movie_genre.movie_id', 'movies.id')
                                ->whereIn('movie_genre.genre_id', $topGenreIds);
                        });
                    }
                    if ($hasYearTaste) {
                        if ($hasGenreTaste) {
                            $innerQuery->orWhereBetween('movies.year', [$yearLower, $yearUpper]);
                        } else {
                            $innerQuery->whereBetween('movies.year', [$yearLower, $yearUpper]);
                        }
                    }
                });
            })
            ->leftJoin('movie_votes as room_likes', function ($join) use ($roomId) {
                $join->on('room_likes.movie_id', '=', 'movies.id')
                    ->where('room_likes.room_id', $roomId)
                    ->where('room_likes.decision', 'up');
            })
            ->when($hasGenreTaste, function ($query) use ($topGenreIds) {
                $query->leftJoin('movie_genre as genre_match', function ($join) use ($topGenreIds) {
                    $join->on('genre_match.movie_id', '=', 'movies.id')
                        ->whereIn('genre_match.genre_id', $topGenreIds);
                });
            })
            ->select([
                'movies.id',
                DB::raw('count(distinct room_likes.room_participant_id) as room_likes_count'),
                DB::raw($genreMatchExpr.' as genre_match_count'),
                DB::raw(
                    '('.$weights['room_likes'].' * count(distinct room_likes.room_participant_id) + '.
                    $weights['genre_match'].' * '.$genreMatchExpr.' + '.
                    $weights['year_match'].' * '.$yearMatchExpr.
                    ') as score'
                ),
            ])
            ->groupBy('movies.id', 'movies.year')
            ->orderByDesc('score')
            ->limit(50)
            ->get();

        if ($candidates->isNotEmpty()) {
            return $candidates->random()->id;
        }

        return Movie::query()
            ->when(! empty($seenMovieIds), fn ($query) => $query->whereNotIn('id', $seenMovieIds))
            ->inRandomOrder()
            ->value('id');
    }
}
