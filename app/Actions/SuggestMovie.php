<?php

namespace App\Actions;

use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Facades\DB;

class SuggestMovie
{
    private const ROOM_LIKE_WEIGHT = 0.3;
    private const GENRE_SCORE_WEIGHT = 0.5;
    private const YEAR_SCORE_WEIGHT = 0.2;
    private const YEAR_SCORE_RANGE = 40.0;
    private const YEAR_SCORE_MAX = 5.0;
    private const SELF_GENRE_VOTE_WEIGHT = 1.8;
    private const OTHER_GENRE_VOTE_WEIGHT = 0.9;
    private const NOVELTY_BONUS = 2.0;
    private const NOVELTY_PENALTY = -2.0;
    private const GENRE_DOMINANCE_THRESHOLD = 0.6;
    private const GENRE_DOMINANCE_MULTIPLIER = 0.6;
    private const CANDIDATE_SAMPLE_SIZE = 1000;

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

        $genreScoreQuery = DB::table('movie_votes')
            ->join('movie_genre', 'movie_genre.movie_id', '=', 'movie_votes.movie_id')
            ->where('movie_votes.room_id', $roomId)
            ->select([
                'movie_genre.genre_id',
                DB::raw(
                    "sum(case when movie_votes.decision = 'up' then ".
                    "(case when movie_votes.room_participant_id = {$participantId} then ".self::SELF_GENRE_VOTE_WEIGHT." else ".self::OTHER_GENRE_VOTE_WEIGHT." end) ".
                    "else -(case when movie_votes.room_participant_id = {$participantId} then ".self::SELF_GENRE_VOTE_WEIGHT." else ".self::OTHER_GENRE_VOTE_WEIGHT." end) end) as score"
                ),
            ])
            ->groupBy('movie_genre.genre_id');

        $genreScores = $genreScoreQuery
            ->pluck('score', 'genre_id')
            ->map(fn ($score) => (float) $score)
            ->all();

        $positiveGenreScores = array_filter($genreScores, fn ($score) => $score > 0);
        $totalPositiveScore = array_sum($positiveGenreScores);
        $maxPositiveScore = $positiveGenreScores ? max($positiveGenreScores) : 0.0;
        $genreScoreMultiplier = ($totalPositiveScore > 0 && ($maxPositiveScore / $totalPositiveScore) > self::GENRE_DOMINANCE_THRESHOLD)
            ? self::GENRE_DOMINANCE_MULTIPLIER
            : 1.0;

        $genreScoreBase = DB::query()->fromSub($genreScoreQuery, 'genre_scores');

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

        $hasGenreTaste = ! empty($topGenreIds);
        $hasYearTaste = $avgYear !== null;
        $hasTaste = $hasGenreTaste || $hasYearTaste;

        $weights = [
            'room_likes' => self::ROOM_LIKE_WEIGHT,
            'genre_score' => self::GENRE_SCORE_WEIGHT * $genreScoreMultiplier,
            'year_score' => self::YEAR_SCORE_WEIGHT,
        ];

        $genreScoreExpr = $hasGenreTaste ? 'coalesce(sum(genre_scores.score), 0)' : '0';
        $yearDeltaExpr = "(case when abs(movies.year - {$avgYear}) < ".self::YEAR_SCORE_RANGE." then abs(movies.year - {$avgYear}) else ".self::YEAR_SCORE_RANGE." end)";
        $yearNormalizedExpr = "(1.0 * {$yearDeltaExpr} / ".self::YEAR_SCORE_RANGE.")";
        $yearScoreExpr = $hasYearTaste
            ? "(".self::YEAR_SCORE_MAX." - (2 * ".self::YEAR_SCORE_MAX.") * {$yearNormalizedExpr} * {$yearNormalizedExpr})"
            : '0';

        $sampleIds = Movie::query()
            ->when(! empty($seenMovieIds), fn ($query) => $query->whereNotIn('movies.id', $seenMovieIds))
            ->when($hasTaste, function ($query) use ($topGenreIds, $avgYear, $hasGenreTaste, $hasYearTaste) {
                $query->where(function ($innerQuery) use ($topGenreIds, $avgYear, $hasGenreTaste, $hasYearTaste) {
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
                            $innerQuery->orWhereBetween('movies.year', [$avgYear - self::YEAR_SCORE_RANGE, $avgYear + self::YEAR_SCORE_RANGE]);
                        } else {
                            $innerQuery->whereBetween('movies.year', [$avgYear - self::YEAR_SCORE_RANGE, $avgYear + self::YEAR_SCORE_RANGE]);
                        }
                    }
                });
            })
            ->inRandomOrder()
            ->limit(self::CANDIDATE_SAMPLE_SIZE)
            ->pluck('movies.id');

        $candidates = Movie::query()
            ->whereIn('movies.id', $sampleIds)
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
                DB::raw($yearScoreExpr.' as year_score'),
                DB::raw(
                    '('.$weights['room_likes'].' * count(distinct room_likes.room_participant_id) + '.
                    $weights['genre_score'].' * '.$genreScoreExpr.' + '.
                    $weights['year_score'].' * '.$yearScoreExpr.
                    ') as score'
                ),
            ])
            ->groupBy('movies.id', 'movies.year')
            ->orderByDesc('score')
            ->limit(50)
            ->get();

        if ($candidates->isNotEmpty()) {
            $likedGenreCounts = DB::table('movie_votes')
                ->join('movie_genre', 'movie_genre.movie_id', '=', 'movie_votes.movie_id')
                ->where('movie_votes.room_id', $roomId)
                ->where('movie_votes.decision', 'up')
                ->select('movie_genre.genre_id', DB::raw('count(*) as total'))
                ->groupBy('movie_genre.genre_id')
                ->pluck('total', 'genre_id')
                ->map(fn ($count) => (int) $count)
                ->all();

            $avgGenreLikes = null;
            if ($likedGenreCounts) {
                $avgGenreLikes = array_sum($likedGenreCounts) / count($likedGenreCounts);
            }

            $candidateGenres = DB::table('movie_genre')
                ->whereIn('movie_id', $candidates->pluck('id')->all())
                ->get(['movie_id', 'genre_id'])
                ->groupBy('movie_id')
                ->map(fn ($rows) => $rows->pluck('genre_id')->all())
                ->all();

            $candidates = $candidates->map(function ($candidate) use ($candidateGenres, $likedGenreCounts, $avgGenreLikes) {
                $genreIds = $candidateGenres[$candidate->id] ?? [];
                $noveltyBonus = 0.0;
                if ($avgGenreLikes !== null && $genreIds !== []) {
                    $hasUnderrepresented = false;
                    $allOverrepresented = true;

                    foreach ($genreIds as $genreId) {
                        $count = $likedGenreCounts[$genreId] ?? 0;
                        if ($count < $avgGenreLikes) {
                            $hasUnderrepresented = true;
                        }
                        if ($count <= $avgGenreLikes) {
                            $allOverrepresented = false;
                        }
                    }

                    if ($hasUnderrepresented) {
                        $noveltyBonus = self::NOVELTY_BONUS;
                    } elseif ($allOverrepresented) {
                        $noveltyBonus = self::NOVELTY_PENALTY;
                    }
                }

                $candidate->novelty_bonus = $noveltyBonus;
                $candidate->adjusted_score = (float) $candidate->score + $noveltyBonus;

                return $candidate;
            });

            $totalScore = $candidates->sum(function ($candidate) {
                return max(0.0, (float) $candidate->adjusted_score);
            });

            if ($totalScore > 0) {
                $threshold = (mt_rand() / mt_getrandmax()) * $totalScore;
                $runningTotal = 0.0;
                $picked = $candidates->first(function ($candidate) use (&$runningTotal, $threshold) {
                    $runningTotal += max(0.0, (float) $candidate->adjusted_score);
                    return $runningTotal >= $threshold;
                });
            } else {
                $picked = $candidates->random();
            }

            return [
                'id' => $picked->id,
                'score' => (float) $picked->adjusted_score,
                'room_likes' => (int) $picked->room_likes_count,
                'genre_score' => (int) $picked->genre_score,
                'year_score' => (float) $picked->year_score,
                'novelty_bonus' => (float) ($picked->novelty_bonus ?? 0.0),
                'genre_score_multiplier' => (float) $genreScoreMultiplier,
                'weights' => $weights,
                'avg_year' => $avgYear,
                'total_score' => (float) $totalScore,
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
            'year_score' => 0,
            'novelty_bonus' => 0,
            'genre_score_multiplier' => (float) $genreScoreMultiplier,
            'weights' => $weights,
            'avg_year' => $avgYear,
            'total_score' => 0,
        ];
    }
}
