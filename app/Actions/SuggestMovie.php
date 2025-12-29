<?php

namespace App\Actions;

use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Facades\DB;

class SuggestMovie
{
    public function execute(int $roomId, int $participantId): ?int
    {
        $selection = $this->selectCandidate($roomId, $participantId);

        return $selection['id'];
    }

    public function executeWithDebug(int $roomId, int $participantId): array
    {
        return $this->selectCandidate($roomId, $participantId);
    }

    protected function selectCandidate(int $roomId, int $participantId): array
    {
        $seenMovieIds = MovieVote::where('room_id', $roomId)
            ->where('room_participant_id', $participantId)
            ->pluck('movie_id')
            ->all();

        $likedMovieIds = MovieVote::where('room_id', $roomId)
            ->where('decision', 'up')
            ->pluck('movie_id')
            ->all();

        $genreScoreBase = DB::table('movie_votes')
            ->join('movie_genre', 'movie_genre.movie_id', '=', 'movie_votes.movie_id')
            ->where('movie_votes.room_id', $roomId)
            ->select([
                'movie_genre.genre_id',
                DB::raw(
                    "sum(case when movie_votes.decision = 'up' then ".
                    "(case when movie_votes.room_participant_id = {$participantId} then 2 else 1 end) ".
                    "else -(case when movie_votes.room_participant_id = {$participantId} then 2 else 1 end) end) as score"
                ),
            ])
            ->groupBy('movie_genre.genre_id');

        $topGenreIds = DB::query()
            ->fromSub($genreScoreBase, 'genre_scores')
            ->where('score', '>', 0)
            ->orderByDesc('score')
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
            'room_likes' => 5,
            'genre_score' => 2,
            'year_match' => 1,
        ];

        $genreScoreExpr = $hasGenreTaste ? 'coalesce(sum(genre_scores.score), 0)' : '0';
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
            ->leftJoin('movie_votes as room_likes', function ($join) use ($roomId, $participantId) {
                $join->on('room_likes.movie_id', '=', 'movies.id')
                    ->where('room_likes.room_id', $roomId)
                    ->where('room_likes.decision', 'up')
                    ->where('room_likes.room_participant_id', '!=', $participantId);
            })
            ->when($hasGenreTaste, function ($query) use ($topGenreIds, $genreScoreBase) {
                $query->leftJoin('movie_genre as genre_match', function ($join) use ($topGenreIds) {
                    $join->on('genre_match.movie_id', '=', 'movies.id')
                        ->whereIn('genre_match.genre_id', $topGenreIds);
                })
                    ->leftJoinSub($genreScoreBase, 'genre_scores', function ($join) {
                        $join->on('genre_scores.genre_id', '=', 'genre_match.genre_id');
                    });
            })
            ->select([
                'movies.id',
                DB::raw('count(distinct room_likes.room_participant_id) as room_likes_count'),
                DB::raw($genreScoreExpr.' as genre_score'),
                DB::raw($yearMatchExpr.' as year_match_count'),
                DB::raw(
                    '('.$weights['room_likes'].' * count(distinct room_likes.room_participant_id) + '.
                    $weights['genre_score'].' * '.$genreScoreExpr.' + '.
                    $weights['year_match'].' * '.$yearMatchExpr.
                    ') as score'
                ),
            ])
            ->groupBy('movies.id', 'movies.year')
            ->orderByDesc('score')
            ->limit(50)
            ->get();

        if ($candidates->isNotEmpty()) {
            $totalScore = $candidates->sum(function ($candidate) {
                return max(0, (int) $candidate->score);
            });

            if ($totalScore > 0) {
                $threshold = random_int(1, $totalScore);
                $runningTotal = 0;
                $picked = $candidates->first(function ($candidate) use (&$runningTotal, $threshold) {
                    $runningTotal += max(0, (int) $candidate->score);
                    return $runningTotal >= $threshold;
                });
            } else {
                $picked = $candidates->random();
            }

            return [
                'id' => $picked->id,
                'score' => (int) $picked->score,
                'room_likes' => (int) $picked->room_likes_count,
                'genre_score' => (int) $picked->genre_score,
                'year_match' => (int) $picked->year_match_count,
                'weights' => $weights,
                'avg_year' => $avgYear,
                'total_score' => (int) $totalScore,
            ];
        }

        $fallbackId = Movie::query()
            ->when(! empty($seenMovieIds), fn ($query) => $query->whereNotIn('id', $seenMovieIds))
            ->inRandomOrder()
            ->value('id');

        return [
            'id' => $fallbackId,
            'score' => 0,
            'room_likes' => 0,
            'genre_score' => 0,
            'year_match' => 0,
            'weights' => $weights,
            'avg_year' => $avgYear,
            'total_score' => 0,
        ];
    }
}
