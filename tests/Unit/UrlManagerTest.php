<?php

namespace Tests\Unit;

use App\Models\Stat\Stat;
use App\Models\Url\Manager\UrlManager;
use App\Models\Url\Manager\UrlShortenerFacade;

use App\Models\Url\Url;
use Tests\TestCase;

/**
 * Class UrlManagerTest
 * @package Tests\Unit
 */
class UrlManagerTest extends TestCase
{

    /**
     * @var int
     */
    protected $randomNumber;
    /**
     * @var Url
     */
    protected $url;
    /**
     * @var Stat
     */
    protected $stats;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->randomNumber = random_int(1, 9999999);
    }

    /**
     *
     */
    protected function tearDown(): void
    {
        if ($this->url) {
            $this->url->delete();
        }

        if ($this->stats) {
            $this->stats->delete();
        }

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * Verifies the creation and validity of a newly created shortened Url
     */
    public function testShorten()
    {
        $original = 'https://www.google.com?r=' . $this->randomNumber;
        $url = \Shortener::shorten($original);
        $this->url = $url;
        $this->assertIsObject($url);
        $this->assertSame($original, $url->getUnfurledUrl());
        $calculatedShortened = \Hashids::encode($url->id);
        $this->assertSame($url->getShortenedString(), $calculatedShortened);
    }

    /**
     * Verifies that if a url is already in DB, it doesn't get created again
     */
    public function testShortenAlreadyInDB()
    {
        $original = 'https://www.google.com?r=' . $this->randomNumber;
        $firstUrl = \Shortener::shorten($original);
        $this->url = $firstUrl;

        $originalToBeChecked = 'https://www.google.com?r=' . $this->randomNumber;
        $secondUrl = \Shortener::shorten($originalToBeChecked);

        $this->assertSame($firstUrl->id, $secondUrl->id);
    }

    /**
     * helper method to create a url without inserting this functionality in the SetUp method.
     */
    protected function secondarySetup()
    {
        $original = 'https://www.google.com?r=' . $this->randomNumber;
        $firstUrl = \Shortener::shorten($original);
        $this->url = $firstUrl;
    }

    /**
     * Verifies the exact corrispondence between a newly created url and a loaded one
     */
    public function testShortenLoadExistingSuccess()
    {
        $this->secondarySetup();
        $url = \Shortener::load($this->url->getShortenedString());

        $this->assertSame($url->id, $this->url->id);
        $this->assertSame($url->url, $this->url->url);
        $this->assertSame($url->shortened, $this->url->shortened);
    }

    /**
     * Verifies that a load operation on a non existent url gives null result
     */
    public function testShortenLoadExistingNotExisting()
    {
        $url = \Shortener::load(\Str::random(12));

        $this->assertNull($url);
    }

    /**
     * verifies the correct behaviour of the deletion process
     */
    public function testDeleteUrl()
    {
        $this->secondarySetup();
        $url = \Shortener::delete($this->url);

        $this->assertSame($url->id, $this->url->id);

        $actual = Url::where('id', $url->id)->first();

        $this->assertNull($actual);
    }

    /**
     * verifies the correct calculation of the cache key
     */
    public function testCacheKeyShortened()
    {
        $randomString = \Str::random(12);
        $cacheKey = \Shortener::cacheKeyForShortenedUrl($randomString);

        $expected = 'url_' . $randomString;
        $this->assertSame($expected, $cacheKey);
    }

    /**
     * verifies the correct calculation of the cache key for the url stats
     */
    public function testCacheKeyShortenedStats()
    {
        $randomString = \Str::random(12);
        $cacheKey = \Shortener::cacheKeyStatsForShortenedUrl($randomString);

        $expected = 'url_stats_' . $randomString;
        $this->assertSame($expected, $cacheKey);
    }

    /**
     * Checks if the number of opens, for a new url, is 0
     */
    public function testRetrieveStatsForUrl()
    {
        $this->secondarySetup();
        $stats = \Shortener::getStatForShortenedUrl($this->url->getShortenedString());
        $this->assertSame(0, $stats->opens);
    }

    /**
     * Checks if the number of opens, for a new url which has just been visited, is 1
     */
    public function testRetrieveStatsForUrlWithOpens()
    {
        $this->secondarySetup();
        $response = $this->get(route('get-original-url', ['hashedId' => $this->url->getShortenedString()]));

        $stats = \Shortener::getStatForShortenedUrl($this->url->getShortenedString());
        $this->assertSame(1, $stats->opens);
    }

    /**
     * verifies the there are null stats for a non existent url
     */
    public function testRetrieveStatsForNonExistentUrl()
    {
        $stats = \Shortener::getStatForShortenedUrl(\Str::random(12));
        $this->assertNull($stats);
    }


}
