<?php

namespace Tests\Feature;

use App\Events\UrlOpenedEvent;
use App\Models\Url\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ShortenerUrlTest
 * @package Tests\Feature
 */
class ShortenerUrlTest extends TestCase
{

    /**
     * @var Url
     */
    protected $url;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        //creates a random Url id
        $dynamicId = random_int(1, 9999999);
        $url = new Url();
        $url->shortened = \Hashids::encode($dynamicId);
        $url->url = "https://www.google.com?r=" . $dynamicId;
        $url->save();
        $this->url = $url;
    }

    /**
     * @throws \Exception
     */
    protected function tearDown(): void
    {
        $this->url->delete();
        parent::tearDown();
    }


    /**
     * Verifies that the redirect and url shortener is working
     */
    public function testShortenedUrl()
    {
        $response = $this->get(route('get-original-url', ['hashedId' => $this->url->getShortenedString()]));
        $this->assertSame($response->headers->get('Location'), $this->url->getUnfurledUrl());
    }

    /**
     * Checks that if a non existend shortened url is called, the response is a 404
     */
    public function testShortenedUrlNotExistent()
    {
        $response = $this->get(route('get-original-url', ['hashedId' => \Str::random(5)]));
        $response->assertStatus(404);
    }


}
