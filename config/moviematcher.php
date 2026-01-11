<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Movie Matching Weights
    |--------------------------------------------------------------------------
    |
    | These weights determine how different factors influence movie suggestions.
    | Each weight represents the relative importance of that factor in the
    | final scoring algorithm.
    |
    */

    'weights' => [
        'room_likes' => (float) env('MOVIEMATCHER_ROOM_LIKE_WEIGHT', 0.45),
        'genre_score' => (float) env('MOVIEMATCHER_GENRE_SCORE_WEIGHT', 0.40),
        'actor_score' => (float) env('MOVIEMATCHER_ACTOR_SCORE_WEIGHT', 0.40),
        'year_score' => (float) env('MOVIEMATCHER_YEAR_SCORE_WEIGHT', 0.20),
        'rating_score' => (float) env('MOVIEMATCHER_RATING_SCORE_WEIGHT', 0.35),
        'popularity_score' => (float) env('MOVIEMATCHER_POPULARITY_SCORE_WEIGHT', 0.15),
        'genre_preference' => (float) env('MOVIEMATCHER_GENRE_PREFERENCE_WEIGHT', 0.30),
        'genre_preference_base' => (float) env('MOVIEMATCHER_GENRE_PREFERENCE_BASE', 1.0),
        'genre_preference_decay' => (float) env('MOVIEMATCHER_GENRE_PREFERENCE_DECAY', 0.5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Vote Weights
    |--------------------------------------------------------------------------
    |
    | These weights determine how much influence votes have on genre/actor
    | preferences, differentiating between the current participant's votes
    | and other participants' votes.
    |
    */

    'tag_vote_weights' => [
        'self' => (float) env('MOVIEMATCHER_SELF_TAG_VOTE_WEIGHT', 1.8),
        'other' => (float) env('MOVIEMATCHER_OTHER_TAG_VOTE_WEIGHT', 0.9),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dominance Parameters
    |--------------------------------------------------------------------------
    |
    | These parameters control how the algorithm responds to highly dominant
    | preferences in genres or actors.
    |
    */

    'dominance' => [
        'genre_threshold' => (float) env('MOVIEMATCHER_GENRE_DOMINANCE_THRESHOLD', 0.60),
        'genre_multiplier' => (float) env('MOVIEMATCHER_GENRE_DOMINANCE_MULTIPLIER', 0.70),
        'actor_threshold' => (float) env('MOVIEMATCHER_ACTOR_DOMINANCE_THRESHOLD', 0.60),
        'actor_multiplier' => (float) env('MOVIEMATCHER_ACTOR_DOMINANCE_MULTIPLIER', 0.70),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scoring Limits
    |--------------------------------------------------------------------------
    |
    | Maximum values for various scoring components.
    |
    */

    'scoring' => [
        'year_max' => (float) env('MOVIEMATCHER_YEAR_SCORE_MAX', 5.0),
        'year_range' => (float) env('MOVIEMATCHER_YEAR_SCORE_RANGE', 40.0),
        'rating_max' => (float) env('MOVIEMATCHER_RATING_SCORE_MAX', 5.0),
        'popularity_max' => (float) env('MOVIEMATCHER_POPULARITY_SCORE_MAX', 5.0),
        'popularity_log_max' => (float) env('MOVIEMATCHER_POPULARITY_LOG_MAX', 4.5),
        'novelty_bonus_max' => (float) env('MOVIEMATCHER_NOVELTY_BONUS_MAX', 1.25),
        'vote_count_threshold' => (int) env('MOVIEMATCHER_VOTE_COUNT_THRESHOLD', 500),
        'vote_average_mean' => (float) env('MOVIEMATCHER_VOTE_AVERAGE_MEAN', 6.0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Selection Limits
    |--------------------------------------------------------------------------
    |
    | These limits control how many items are considered at various stages
    | of the recommendation process.
    |
    */

    'limits' => [
        'top_genres' => (int) env('MOVIEMATCHER_TOP_GENRE_LIMIT', 5),
        'top_actors' => (int) env('MOVIEMATCHER_TOP_ACTOR_LIMIT', 7),
        'search_fetch_size' => (int) env('MOVIEMATCHER_SEARCH_FETCH_SIZE', 2500),
        'candidate_sample_size' => (int) env('MOVIEMATCHER_CANDIDATE_SAMPLE_SIZE', 1000),
        'candidate_limit' => (int) env('MOVIEMATCHER_CANDIDATE_LIMIT', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Behavior Parameters
    |--------------------------------------------------------------------------
    |
    | Parameters controlling the overall behavior of the recommendation engine.
    |
    */

    'exploration_probability' => (float) env('MOVIEMATCHER_EXPLORATION_PROBABILITY', 0.15),
    'taste_cache_ttl_seconds' => (int) env('MOVIEMATCHER_TASTE_CACHE_TTL_SECONDS', 60),
];
