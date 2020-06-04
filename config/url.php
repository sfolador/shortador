<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Time to live - represents the time in seconds the url should stay in cache
    | before being refreshed.
    | Defaults to 120 seconds: 2 minutes.
    |
    */

    'url_cache_duration' => env('URL_CACHE_DURATION', 120),
    'url_cache_duration_stats' => env('URL_CACHE_DURATION_STATS', 20),


];
