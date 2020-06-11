<?php

namespace Tests\Feature;

use App\Models\Url\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ShortenerApiStatTest
 * @package Tests\Feature
 */
class ShortenerApiStatTest extends TestCase
{
    /**
     * @var array
     */
    protected $urls = [];
    protected $url;
    /**
     * @var int
     */
    protected $randomNumber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->randomNumber = random_int(1, 9999999);
        $original = "https://www.google.com?r=" . $this->randomNumber;
        $this->urls[] = $original;
        $response = $this->postJson(route('create-url'), ['url' => $original]);
        //get id from DB
        $url = Url::where('url', $original)->first();
        if (!$url) {
            $this->assertFalse(true);
        }
        $this->url = $url;
    }

    protected function tearDown(): void
    {
        if (!empty($this->urls)) {
            Url::whereIn('url', $this->urls)->delete();
        }

        parent::tearDown();
    }

    /**
     * Creates a url and checks the status of the response
     */
    public function testGetUrlStatsWithSuccess()
    {
        $response = $this->get(route('show-url-stats', ['hashedId' => $this->url->getShortenedString()]));
        $response->assertStatus(201);
    }

    /**
     * Creates a url and checks the json structures and values
     */
    public function testGetStatsUrlJson()
    {

        $response = $this->get(route('show-url-stats', ['hashedId' => $this->url->getShortenedString()]));
        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'url' => [
                        'url' => $this->url->getUnfurledUrl(),
                        'shortened' => route('get-original-url', ['hashedId' => $this->url->getShortenedString()])
                    ],
                    "opens" => 0
                ]
            ]
        );
    }

    /**
     * Creates a url and checks the json structures and values
     */
    public function testOpensStatsUrlJson()
    {


        $this->get(route('get-original-url', ['hashedId' => $this->url->getShortenedString()]));

        $response = $this->get(route('show-url-stats', ['hashedId' => $this->url->getShortenedString()]));
        $response->assertStatus(200)->assertJson(
            [
                'data' => [
                    'url' => [
                        'url' => $this->url->getUnfurledUrl(),
                        'shortened' => route('get-original-url', ['hashedId' => $this->url->getShortenedString()])
                    ],
                    "opens" => 1
                ]
            ]
        );
    }


}
