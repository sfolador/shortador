<?php

namespace App\Models\Url\Manager;


use App\Events\UrlCreatedEvent;
use App\Events\UrlDeletedEvent;
use App\Models\Stat\Stat;
use App\Models\Url\Url;
use Cache;
use Hashids;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class UrlManager
 *
 * The main class of the Application. It provides features and functionality to create shortened Url, to save them, to retrieve them from the cache or the DB.
 *
 * @package App\Models\Url\Manager
 */
class UrlManager
{
    /**
     * @var string
     */
    protected $cacheKey = 'url_';
    /**
     * @var string
     */
    protected $cacheKeyForStats = 'url_stats_';
    /**
     * @var Repository|Application|int|mixed
     */
    protected $duration = 0;
    /**
     * @var Repository|Application|int|mixed
     */
    protected $durationForStats = 0;

    /**
     * UrlManager constructor.
     */
    public function __construct()
    {
        $this->duration = config('url.url_cache_duration');
        $this->durationForStats = config('url.url_cache_duration_stats');
    }


    /**
     * @param $plainUrl
     * @return Url
     * @noinspection PhpUndefinedFieldInspection
     */
    public function shorten($plainUrl): Url
    {

        $alreadyInDB = Url::where('url',$plainUrl)->first();

        if (!$alreadyInDB){
            $url = new Url();
            $url->url = $plainUrl;
            $url->shortened = "-";
            $url->save();

            $url->shortened = $this->generateRandomString($url->id);
            $url->save();
        }else{
            $url = $alreadyInDB;
        }



        $cacheKey = $this->cacheKeyForShortenedUrl($url->shortened);
        Cache::put($cacheKey, $url, $this->duration);
        event(new UrlCreatedEvent($url));
        return $url;
    }

    /**
     * @param Url $url
     * @return Url
     */
    public function delete(Url $url): Url
    {
        event(new UrlDeletedEvent($url));
        $this->removeUrlFromCacheAndDb($url->getShortenedString());
        return $url;
    }

    /**
     * @param $id
     * @return string
     */
    protected function generateRandomString($id): string
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        return Hashids::encode($id);
    }

    /**
     * @param $shortenedUrl
     * @return Url|null
     */
    public function load($shortenedUrl): ?Url
    {
        return $this->loadUrlFromCacheOrDb($shortenedUrl);
    }

    /**
     * @param $shortened
     * @return string
     */
    public function cacheKeyForShortenedUrl($shortened): string
    {
        return $this->cacheKey . $shortened;
    }

    /**
     * @param $shortened
     * @return string
     */
    public function cacheKeyStatsForShortenedUrl($shortened): string
    {
        return $this->cacheKeyForStats . $shortened;
    }

    /**
     * @param $shortenedUrl
     * @return Url|null
     */
    protected function loadUrlFromCacheOrDb($shortenedUrl): ?Url
    {
        $cacheKey = $this->cacheKeyForShortenedUrl($shortenedUrl);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $url = Url::shortenedIs($shortenedUrl)->first();

        if (!$url) {
            return null;
        }

        Cache::put($cacheKey, $url, $this->duration);
        return $url;
    }


    /**
     * @param $shortenedUrl
     * @return void
     */
    protected function removeUrlFromCacheAndDb($shortenedUrl): void
    {
        $cacheKey = $this->cacheKeyForShortenedUrl($shortenedUrl);
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $url = Url::shortenedIs($shortenedUrl);
        if (!$url) {
            return;
        }

        $url->delete();
    }


    /**
     * @param $shortened
     * @return Stat | null
     */
    public function getStatForShortenedUrl($shortened): ?Stat
    {
        $cacheKey = $this->cacheKeyStatsForShortenedUrl($shortened);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $stat = Stat::shortenedUrlIs($shortened)->with('url')->first();
        Cache::put($cacheKey, $stat, $this->durationForStats);

        return $stat;
    }

}
