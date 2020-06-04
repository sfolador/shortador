<?php


namespace App\Models\Url\Manager;


use App\Events\UrlCreatedEvent;
use App\Events\UrlDeletedEvent;
use App\Models\Stat\Stat;
use App\Models\Url\Url;

class UrlManager
{
    protected $cacheKey = 'url_';
    protected $cacheKeyForStats = 'url_stats_';
    protected $duration = 0;
    protected $durationForStats = 0;

    public function __construct()
    {
        $this->duration = config('url.url_cache_duration');
        $this->durationForStats = config('url.url_cache_duration_stats');
    }


    public function shorten($plainUrl): Url
    {
        $url = new Url();
        $url->url = $plainUrl;
        $url->shortened = "-";
        $url->save();

        $url->shortened = $this->generateRandomString($url->id);
        $url->save();

        $cacheKey = $this->cacheKeyForShortenedUrl($url->shortened);
        \Cache::put($cacheKey, $url, $this->duration);
        event(new UrlCreatedEvent($url));
        return $url;
    }

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
        return \Hashids::encode($id);
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

        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }
        $url = Url::shortenedIs($shortenedUrl)->first();

        if (!$url) {
            return null;
        }

        \Cache::put($cacheKey, $url, $this->duration);
        return $url;
    }


    /**
     * @param $shortenedUrl
     * @return void
     */
    protected function removeUrlFromCacheAndDb($shortenedUrl): void
    {
        $cacheKey = $this->cacheKeyForShortenedUrl($shortenedUrl);
        if (\Cache::has($cacheKey)) {
            \Cache::forget($cacheKey);
        }
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
        if (\Cache::has($cacheKey)) {
            return \Cache::get($cacheKey);
        }
        $stat = Stat::shortenedUrlIs($shortened)->with('url')->first();
        \Cache::put($cacheKey, $stat, $this->durationForStats);

        return $stat;
    }

}
