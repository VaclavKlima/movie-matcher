<?php

namespace App\Actions;

use App\Models\Movie;
use App\Models\MovieVote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class SuggestMovie
{
    private const float ROOM_LIKE_WEIGHT = 0.30;

    private const float GENRE_SCORE_WEIGHT = 0.40;

    private const float ACTOR_SCORE_WEIGHT = 0.40;

    private const float YEAR_SCORE_WEIGHT = 0.20;

    private const float YEAR_SCORE_RANGE = 40.0;

    private const float YEAR_SCORE_MAX = 5.0;

    private const float SELF_TAG_VOTE_WEIGHT = 1.8;

    private const float OTHER_TAG_VOTE_WEIGHT = 0.9;

    private const float NOVELTY_BONUS_MAX = 1.25;

    private const float GENRE_DOMINANCE_THRESHOLD = 0.60;

    private const float GENRE_DOMINANCE_MULTIPLIER = 0.70;

    private const float ACTOR_DOMINANCE_THRESHOLD = 0.60;

    private const float ACTOR_DOMINANCE_MULTIPLIER = 0.70;

    private const int TOP_GENRE_LIMIT = 5;

    private const int TOP_ACTOR_LIMIT = 7;

    private const int CANDIDATE_SAMPLE_SIZE = 1000;

    private const int CANDIDATE_LIMIT = 50;

    private const float EXPLORATION_PROBABILITY = 0.15;

    private const float RATING_SCORE_WEIGHT = 0.20;

    private const float FILM_RANK_WEIGHT = 0.20;

    private const float POPULARITY_RANK_WEIGHT = 0.20;

    private const float RATING_SCORE_MAX = 5.0;

    private const float RANK_SCORE_MAX = 5.0;

    private const float RANK_SCORE_RANGE = 1000.0;

    private const int TASTE_CACHE_TTL_SECONDS = 60;

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
        $roomCacheTag = $this->roomCacheTag($roomId);

        $tasteProfile = Cache::tags([$roomCacheTag])->remember(
            $this->tasteCacheKey($roomId, $participantId),
            self::TASTE_CACHE_TTL_SECONDS,
            fn () => $this->buildTasteProfile($roomId, $participantId)
        );

        $isExploration = (mt_rand() / mt_getrandmax()) < self::EXPLORATION_PROBABILITY;

        $candidateQuery = $this->buildCandidateQuery(
            roomId: $roomId,
            participantId: $participantId,
            tasteProfile: $tasteProfile,
            isExploration: $isExploration,
        );

        $candidates = $candidateQuery->limit(self::CANDIDATE_LIMIT)->get();

        if ($candidates->isEmpty()) {
            $fallbackQuery = $this->buildFallbackQuery($roomId, $participantId);
            $fallbackCandidates = $fallbackQuery->limit(self::CANDIDATE_LIMIT)->get();

            if ($fallbackCandidates->isEmpty()) {
                return [
                    'id' => null,
                    'reason' => 'no_candidates',
                ];
            }

            $pickedFallback = $this->weightedRandomPick($fallbackCandidates, 'popularity_score');

            return [
                'id' => $pickedFallback->id,
                'reason' => 'fallback_popularity',
            ];
        }

        $pickedCandidate = $this->weightedRandomPick($candidates, 'adjusted_score');

        return [
            'id' => $pickedCandidate->id,
            'reason' => $isExploration ? 'exploration' : 'ranked',

            'score' => (float) ($pickedCandidate->adjusted_score ?? 0.0),
            'room_likes' => (int) ($pickedCandidate->room_likes_count ?? 0),

            'genre_score' => (float) ($pickedCandidate->genre_score ?? 0.0),
            'actor_score' => (float) ($pickedCandidate->actor_score ?? 0.0),
            'year_score' => (float) ($pickedCandidate->year_score ?? 0.0),

            'rating_score' => (float) ($pickedCandidate->rating_score ?? 0.0),
            'film_rank_score' => (float) ($pickedCandidate->film_rank_score ?? 0.0),
            'film_popularity_score' => (float) ($pickedCandidate->film_popularity_score ?? 0.0),

            'novelty_bonus' => (float) ($pickedCandidate->novelty_bonus ?? 0.0),

            'avg_year' => $tasteProfile['avg_year'],
            'weights' => $tasteProfile['weights'],

            'multipliers' => [
                'genre' => $tasteProfile['genre_score_multiplier'],
                'actor' => $tasteProfile['actor_score_multiplier'],
            ],
        ];
    }

    /**
     * Call this after a vote is created/updated:
     * Cache::tags([$this->roomCacheTag($roomId)])->flush();
     */
    public function roomCacheTag(int $roomId): string
    {
        return "movie_matcher_room_{$roomId}";
    }

    private function tasteCacheKey(int $roomId, int $participantId): string
    {
        return "movie_matcher_taste_profile_room_{$roomId}_participant_{$participantId}";
    }

    private function buildTasteProfile(int $roomId, int $participantId): array
    {
        $likedMovieIds = MovieVote::query()
            ->where('room_id', $roomId)
            ->where('decision', 'up')
            ->pluck('movie_id')
            ->all();

        $averageLikedYear = null;
        if (! empty($likedMovieIds)) {
            $averageLikedYearValue = Movie::query()->whereIn('id', $likedMovieIds)->avg('year');
            $averageLikedYear = $averageLikedYearValue ? (int) round($averageLikedYearValue) : null;
        }

        $genreScoresByGenreId = $this->buildTagScoresByTagId(
            roomId: $roomId,
            participantId: $participantId,
            pivotTableName: 'movie_genre',
            pivotTagColumnName: 'genre_id',
        );

        $actorScoresByActorId = $this->buildTagScoresByTagId(
            roomId: $roomId,
            participantId: $participantId,
            pivotTableName: 'movie_actor',
            pivotTagColumnName: 'actor_id',
        );

        $topGenreIds = $this->topPositiveTagIds($genreScoresByGenreId, self::TOP_GENRE_LIMIT);
        $topActorIds = $this->topPositiveTagIds($actorScoresByActorId, self::TOP_ACTOR_LIMIT);

        $genreScoreMultiplier = $this->dominanceMultiplier(
            positiveScoresById: $genreScoresByGenreId,
            dominanceThreshold: self::GENRE_DOMINANCE_THRESHOLD,
            dominanceMultiplier: self::GENRE_DOMINANCE_MULTIPLIER,
        );

        $actorScoreMultiplier = $this->dominanceMultiplier(
            positiveScoresById: $actorScoresByActorId,
            dominanceThreshold: self::ACTOR_DOMINANCE_THRESHOLD,
            dominanceMultiplier: self::ACTOR_DOMINANCE_MULTIPLIER,
        );

        $weights = [
            'room_likes' => self::ROOM_LIKE_WEIGHT,

            'genre_score' => self::GENRE_SCORE_WEIGHT * $genreScoreMultiplier,
            'actor_score' => self::ACTOR_SCORE_WEIGHT * $actorScoreMultiplier,

            'year_score' => self::YEAR_SCORE_WEIGHT,

            'rating_score' => self::RATING_SCORE_WEIGHT,
            'film_rank_score' => self::FILM_RANK_WEIGHT,
            'film_popularity_score' => self::POPULARITY_RANK_WEIGHT,
        ];

        return [
            'avg_year' => $averageLikedYear,

            'genre_scores_by_id' => $genreScoresByGenreId,
            'actor_scores_by_id' => $actorScoresByActorId,

            'top_genre_ids' => $topGenreIds,
            'top_actor_ids' => $topActorIds,

            'genre_score_multiplier' => $genreScoreMultiplier,
            'actor_score_multiplier' => $actorScoreMultiplier,

            'weights' => $weights,
        ];
    }

    private function buildTagScoresByTagId(
        int $roomId,
        int $participantId,
        string $pivotTableName,
        string $pivotTagColumnName
    ): array {
        $tagScoreQuery = DB::table('movie_votes')
            ->join($pivotTableName, "{$pivotTableName}.movie_id", '=', 'movie_votes.movie_id')
            ->where('movie_votes.room_id', $roomId)
            ->select(["{$pivotTableName}.{$pivotTagColumnName} as tag_id"])
            ->selectRaw(
                "sum(
                    case
                        when movie_votes.decision = 'up' then
                            case when movie_votes.room_participant_id = ? then ? else ? end
                        else
                            -case when movie_votes.room_participant_id = ? then ? else ? end
                    end
                ) as score",
                [
                    $participantId, self::SELF_TAG_VOTE_WEIGHT, self::OTHER_TAG_VOTE_WEIGHT,
                    $participantId, self::SELF_TAG_VOTE_WEIGHT, self::OTHER_TAG_VOTE_WEIGHT,
                ]
            )
            ->groupBy("{$pivotTableName}.{$pivotTagColumnName}");

        return $tagScoreQuery
            ->pluck('score', 'tag_id')
            ->map(fn ($score) => (float) $score)
            ->all();
    }

    private function topPositiveTagIds(array $scoresById, int $limit): array
    {
        $positiveScoresById = array_filter($scoresById, fn (float $score) => $score > 0.0);

        arsort($positiveScoresById);

        return array_slice(array_keys($positiveScoresById), 0, $limit);
    }

    private function dominanceMultiplier(array $positiveScoresById, float $dominanceThreshold, float $dominanceMultiplier): float
    {
        $positiveScores = array_filter($positiveScoresById, fn (float $score) => $score > 0.0);

        if (empty($positiveScores)) {
            return 1.0;
        }

        $totalPositiveScore = array_sum($positiveScores);
        $maxPositiveScore = max($positiveScores);

        if ($totalPositiveScore <= 0.0) {
            return 1.0;
        }

        $dominanceRatio = $maxPositiveScore / $totalPositiveScore;

        return $dominanceRatio > $dominanceThreshold ? $dominanceMultiplier : 1.0;
    }

    private function buildCandidateQuery(int $roomId, int $participantId, array $tasteProfile, bool $isExploration)
    {
        $averageLikedYear = $tasteProfile['avg_year'];
        $topGenreIds = $tasteProfile['top_genre_ids'];
        $topActorIds = $tasteProfile['top_actor_ids'];

        $hasGenreTaste = ! empty($topGenreIds);
        $hasActorTaste = ! empty($topActorIds);
        $hasYearTaste = $averageLikedYear !== null;
        $hasTaste = $hasGenreTaste || $hasActorTaste || $hasYearTaste;

        $weights = $tasteProfile['weights'];

        $ratingScoreExpression = '(case when movies.average_rating is null then 0 else ('.self::RATING_SCORE_MAX.' * (movies.average_rating / 100.0)) end)';
        $filmRankScoreExpression = '(case when movies.film_rank is null then 0 else ('.self::RANK_SCORE_MAX.' * (1 - ((movies.film_rank - 1) / '.self::RANK_SCORE_RANGE.'))) end)';
        $filmPopularityScoreExpression = '(case when movies.film_popularity_rank is null then 0 else ('.self::RANK_SCORE_MAX.' * (1 - ((movies.film_popularity_rank - 1) / '.self::RANK_SCORE_RANGE.'))) end)';
        $popularityScoreExpression = "({$ratingScoreExpression} + {$filmRankScoreExpression} + {$filmPopularityScoreExpression})";

        /**
         * FIX: No bindings here. We embed an integer literal to avoid placeholder duplication.
         */
        $yearScoreExpression = '0';
        if ($hasYearTaste) {
            $averageLikedYearInteger = (int) $averageLikedYear;

            $yearDeltaExpression =
                "(case when abs(CAST(movies.year AS SIGNED) - {$averageLikedYearInteger}) < ".self::YEAR_SCORE_RANGE.' '.
                "then abs(CAST(movies.year AS SIGNED) - {$averageLikedYearInteger}) else ".self::YEAR_SCORE_RANGE.' end)';

            $yearNormalizedExpression = "(1.0 * {$yearDeltaExpression} / ".self::YEAR_SCORE_RANGE.')';

            $yearScoreExpression =
                '('.self::YEAR_SCORE_MAX.' - (2 * '.self::YEAR_SCORE_MAX.") * {$yearNormalizedExpression} * {$yearNormalizedExpression})";
        }

        $sampleSetQuery = Movie::query()
            ->select('movies.id')
            ->selectRaw($popularityScoreExpression.' as popularity_score')
            ->whereNotExists(function ($subquery) use ($roomId, $participantId) {
                $subquery->selectRaw('1')
                    ->from('movie_votes as mv_exclude')
                    ->whereColumn('mv_exclude.movie_id', 'movies.id')
                    ->where('mv_exclude.room_id', $roomId)
                    ->where(function ($innerQuery) use ($participantId) {
                        $innerQuery
                            ->where('mv_exclude.room_participant_id', $participantId)
                            ->orWhere('mv_exclude.decision', 'down');
                    });
            })
            ->when($hasTaste && ! $isExploration, function ($query) use ($hasGenreTaste, $hasActorTaste, $hasYearTaste, $topGenreIds, $topActorIds, $averageLikedYear) {
                $query->where(function ($innerQuery) use ($hasGenreTaste, $hasActorTaste, $hasYearTaste, $topGenreIds, $topActorIds, $averageLikedYear) {
                    $hasAnyConstraint = false;

                    if ($hasGenreTaste) {
                        $hasAnyConstraint = true;

                        $innerQuery->whereExists(function ($subquery) use ($topGenreIds) {
                            $subquery->selectRaw('1')
                                ->from('movie_genre')
                                ->whereColumn('movie_genre.movie_id', 'movies.id')
                                ->whereIn('movie_genre.genre_id', $topGenreIds);
                        });
                    }

                    if ($hasActorTaste) {
                        if ($hasAnyConstraint) {
                            $innerQuery->orWhereExists(function ($subquery) use ($topActorIds) {
                                $subquery->selectRaw('1')
                                    ->from('movie_actor')
                                    ->whereColumn('movie_actor.movie_id', 'movies.id')
                                    ->whereIn('movie_actor.actor_id', $topActorIds);
                            });
                        } else {
                            $hasAnyConstraint = true;

                            $innerQuery->whereExists(function ($subquery) use ($topActorIds) {
                                $subquery->selectRaw('1')
                                    ->from('movie_actor')
                                    ->whereColumn('movie_actor.movie_id', 'movies.id')
                                    ->whereIn('movie_actor.actor_id', $topActorIds);
                            });
                        }
                    }

                    if ($hasYearTaste) {
                        $yearRangeMinimum = ((int) $averageLikedYear) - (int) self::YEAR_SCORE_RANGE;
                        $yearRangeMaximum = ((int) $averageLikedYear) + (int) self::YEAR_SCORE_RANGE;

                        if ($hasAnyConstraint) {
                            $innerQuery->orWhereBetween('movies.year', [$yearRangeMinimum, $yearRangeMaximum]);
                        } else {
                            $innerQuery->whereBetween('movies.year', [$yearRangeMinimum, $yearRangeMaximum]);
                        }
                    }
                });
            })
            ->orderByDesc('popularity_score')
            ->limit(self::CANDIDATE_SAMPLE_SIZE);

        $genreScoreBaseQuery = $this->buildScoreBaseQuery($tasteProfile['genre_scores_by_id']);
        $actorScoreBaseQuery = $this->buildScoreBaseQuery($tasteProfile['actor_scores_by_id']);

        $genreScoreExpression = $hasGenreTaste ? 'coalesce(sum(genre_scores.score), 0)' : '0';
        $actorScoreExpression = $hasActorTaste ? 'coalesce(sum(actor_scores.score), 0)' : '0';

        $genreLikeCountsSubquery = $this->buildTagLikeCountsSubquery(
            roomId: $roomId,
            pivotTableName: 'movie_genre',
            pivotTagColumnName: 'genre_id',
        );
        $genreLikeAverageSubquery = $this->buildAverageFromCountsSubquery($genreLikeCountsSubquery, 'genre_totals');

        $actorLikeCountsSubquery = $this->buildTagLikeCountsSubquery(
            roomId: $roomId,
            pivotTableName: 'movie_actor',
            pivotTagColumnName: 'actor_id',
        );
        $actorLikeAverageSubquery = $this->buildAverageFromCountsSubquery($actorLikeCountsSubquery, 'actor_totals');

        $genreNoveltyExpression = '0';
        if ($hasGenreTaste) {
            $genreNoveltyExpression = 'coalesce(avg(greatest(0, genre_like_averages.avg_total - coalesce(genre_like_counts.total, 0))), 0)';
        }

        $actorNoveltyExpression = '0';
        if ($hasActorTaste) {
            $actorNoveltyExpression = 'coalesce(avg(greatest(0, actor_like_averages.avg_total - coalesce(actor_like_counts.total, 0))), 0)';
        }

        $noveltyBonusExpression = '(least('.self::NOVELTY_BONUS_MAX.", ({$genreNoveltyExpression} + {$actorNoveltyExpression}) / 2))";

        $baseScoreExpression =
            '('.
            $weights['room_likes'].' * count(distinct room_likes.room_participant_id) + '.
            $weights['genre_score'].' * '.$genreScoreExpression.' + '.
            $weights['actor_score'].' * '.$actorScoreExpression.' + '.
            $weights['year_score'].' * '.$yearScoreExpression.' + '.
            $weights['rating_score'].' * '.$ratingScoreExpression.' + '.
            $weights['film_rank_score'].' * '.$filmRankScoreExpression.' + '.
            $weights['film_popularity_score'].' * '.$filmPopularityScoreExpression.
            ')';

        $candidateQuery = Movie::query()
            ->joinSub($sampleSetQuery, 'sample_set', function ($join) {
                $join->on('sample_set.id', '=', 'movies.id');
            })
            ->leftJoin('movie_votes as room_likes', function ($join) use ($roomId, $participantId) {
                $join->on('room_likes.movie_id', '=', 'movies.id')
                    ->where('room_likes.room_id', $roomId)
                    ->where('room_likes.decision', 'up')
                    ->where('room_likes.room_participant_id', '!=', $participantId);
            });

        if ($hasGenreTaste) {
            $candidateQuery
                ->leftJoin('movie_genre as genre_match', function ($join) use ($topGenreIds) {
                    $join->on('genre_match.movie_id', '=', 'movies.id')
                        ->whereIn('genre_match.genre_id', $topGenreIds);
                })
                ->leftJoinSub($genreScoreBaseQuery, 'genre_scores', function ($join) {
                    $join->on('genre_scores.tag_id', '=', 'genre_match.genre_id');
                })
                ->leftJoinSub($genreLikeCountsSubquery, 'genre_like_counts', function ($join) {
                    $join->on('genre_like_counts.tag_id', '=', 'genre_match.genre_id');
                })
                ->crossJoinSub($genreLikeAverageSubquery, 'genre_like_averages');
        }

        if ($hasActorTaste) {
            $candidateQuery
                ->leftJoin('movie_actor as actor_match', function ($join) use ($topActorIds) {
                    $join->on('actor_match.movie_id', '=', 'movies.id')
                        ->whereIn('actor_match.actor_id', $topActorIds);
                })
                ->leftJoinSub($actorScoreBaseQuery, 'actor_scores', function ($join) {
                    $join->on('actor_scores.tag_id', '=', 'actor_match.actor_id');
                })
                ->leftJoinSub($actorLikeCountsSubquery, 'actor_like_counts', function ($join) {
                    $join->on('actor_like_counts.tag_id', '=', 'actor_match.actor_id');
                })
                ->crossJoinSub($actorLikeAverageSubquery, 'actor_like_averages');
        }

        return $candidateQuery
            ->select(['movies.id'])
            ->selectRaw('count(distinct room_likes.room_participant_id) as room_likes_count')
            ->selectRaw($genreScoreExpression.' as genre_score')
            ->selectRaw($actorScoreExpression.' as actor_score')
            ->selectRaw($yearScoreExpression.' as year_score')
            ->selectRaw($ratingScoreExpression.' as rating_score')
            ->selectRaw($filmRankScoreExpression.' as film_rank_score')
            ->selectRaw($filmPopularityScoreExpression.' as film_popularity_score')
            ->selectRaw($noveltyBonusExpression.' as novelty_bonus')
            ->selectRaw($baseScoreExpression.' as score')
            ->selectRaw('('.$baseScoreExpression.' + '.$noveltyBonusExpression.') as adjusted_score')
            ->groupBy('movies.id')
            ->orderByDesc('adjusted_score');
    }

    private function buildFallbackQuery(int $roomId, int $participantId)
    {
        $ratingScoreExpression = '(case when movies.average_rating is null then 0 else ('.self::RATING_SCORE_MAX.' * (movies.average_rating / 100.0)) end)';
        $filmRankScoreExpression = '(case when movies.film_rank is null then 0 else ('.self::RANK_SCORE_MAX.' * (1 - ((movies.film_rank - 1) / '.self::RANK_SCORE_RANGE.'))) end)';
        $filmPopularityScoreExpression = '(case when movies.film_popularity_rank is null then 0 else ('.self::RANK_SCORE_MAX.' * (1 - ((movies.film_popularity_rank - 1) / '.self::RANK_SCORE_RANGE.'))) end)';
        $popularityScoreExpression = "({$ratingScoreExpression} + {$filmRankScoreExpression} + {$filmPopularityScoreExpression})";

        return Movie::query()
            ->select('movies.id')
            ->selectRaw($popularityScoreExpression.' as popularity_score')
            ->whereNotExists(function ($subquery) use ($roomId, $participantId) {
                $subquery->selectRaw('1')
                    ->from('movie_votes as mv_exclude')
                    ->whereColumn('mv_exclude.movie_id', 'movies.id')
                    ->where('mv_exclude.room_id', $roomId)
                    ->where(function ($innerQuery) use ($participantId) {
                        $innerQuery
                            ->where('mv_exclude.room_participant_id', $participantId)
                            ->orWhere('mv_exclude.decision', 'down');
                    });
            })
            ->orderByDesc('popularity_score');
    }

    private function buildTagLikeCountsSubquery(int $roomId, string $pivotTableName, string $pivotTagColumnName)
    {
        return DB::table('movie_votes')
            ->join($pivotTableName, "{$pivotTableName}.movie_id", '=', 'movie_votes.movie_id')
            ->where('movie_votes.room_id', $roomId)
            ->where('movie_votes.decision', 'up')
            ->selectRaw("{$pivotTableName}.{$pivotTagColumnName} as tag_id, count(*) as total")
            ->groupBy("{$pivotTableName}.{$pivotTagColumnName}");
    }

    private function buildAverageFromCountsSubquery($countsSubquery, string $alias): \Illuminate\Database\Query\Builder
    {
        return DB::query()
            ->fromSub($countsSubquery, $alias)
            ->selectRaw('avg(total) as avg_total');
    }

    private function buildScoreBaseQuery(array $scoresById)
    {
        if (empty($scoresById)) {
            return DB::query()
                ->selectRaw('null as tag_id, 0 as score')
                ->whereRaw('1 = 0');
        }

        $rows = collect($scoresById)
            ->map(fn (float $score, int|string $tagId) => ['tag_id' => (int) $tagId, 'score' => (float) $score])
            ->values();

        $firstRow = $rows->first();

        $baseQuery = DB::query()->selectRaw('? as tag_id, ? as score', [$firstRow['tag_id'], $firstRow['score']]);

        foreach ($rows->slice(1) as $row) {
            $baseQuery->unionAll(DB::query()->selectRaw('? as tag_id, ? as score', [$row['tag_id'], $row['score']]));
        }

        return $baseQuery;
    }

    private function weightedRandomPick(Collection $candidates, string $scoreColumn)
    {
        $totalScore = $candidates->sum(function ($candidate) use ($scoreColumn): float {
            return max(0.0, (float) ($candidate->{$scoreColumn} ?? 0.0));
        });

        if ($totalScore <= 0.0) {
            return $candidates->random();
        }

        $threshold = (mt_rand() / mt_getrandmax()) * $totalScore;
        $runningTotal = 0.0;

        return $candidates->first(function ($candidate) use (&$runningTotal, $threshold, $scoreColumn): bool {
            $runningTotal += max(0.0, (float) ($candidate->{$scoreColumn} ?? 0.0));

            return $runningTotal >= $threshold;
        }) ?? $candidates->random();
    }
}
