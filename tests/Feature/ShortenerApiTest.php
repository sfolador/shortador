<?php

namespace Tests\Feature;

use App\Models\Url\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ShortenerApiTest
 * @package Tests\Feature
 */
class ShortenerApiTest extends TestCase
{
    /**
     * @var array
     */
    protected $urls = [];
    /**
     * @var int
     */
    protected $randomNumber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->randomNumber = random_int(1, 9999999);
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
    public function testCreateUrlWithSuccess()
    {
        $original = "https://www.google.com?r=" . $this->randomNumber;
        $this->urls[] = $original;
        $response = $this->postJson(route('create-url'), ['url' => $original]);
        $response->assertStatus(201);
    }


    protected function secondarySetUp()
    {
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

    /**
     * Creates a url and checks the json structures and values
     */
    public function testCreateUrlJson()
    {
        $original = "https://www.google.com?r=" . $this->randomNumber;
        $this->urls[] = $original;
        $response = $this->postJson(route('create-url'), ['url' => $original]);
        //get id from DB
        $url = Url::where('url', $original)->first();
        if (!$url) {
            $this->assertFalse(true);
        }
        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'url' => $original,
                    'shortened' => route('get-original-url', ['hashedId' => $url->getShortenedString()])
                ]
            ]
        );
    }

    /**
     * Checks if validation on url parameter is working
     */
    public function testCreateUrlFailedUrlValidation()
    {
        //generate a random string instead of a valid URL
        $original = \Str::random(12);
        $response = $this->postJson(route('create-url'), ['url' => $original]);
        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'url' => [
                        __('validation.url', ['attribute' => 'url'])
                    ]
                ]
            ]
        );
    }

    /**
     * Checks if validation on url parameter is working
     */
    public function testCreateUrlFailedRequiredValidation()
    {
        //generate a random string instead of a valid URL
        $original = \Str::random(12);
        $response = $this->postJson(route('create-url'), ['url' => ' ']);
        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'url' => [
                        __('validation.required', ['attribute' => 'url'])
                    ]
                ]
            ]
        );
    }


    /**
     * Retrieve data about a Url and checks the json structures and values
     */
    public function testShowUrlJson()
    {
       $this->secondarySetUp();

        $response = $this->get(route('show-url', ['hashedId' => $this->url->getShortenedString()]));

        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'url' => $this->url->getUnfurledUrl(),
                    'shortened' => route('get-original-url', ['hashedId' => $this->url->getShortenedString()])
                ]
            ]
        );
    }

    /**
     * Retrieve data about a Url and checks the json structures and values
     */
    public function testDeleteUrlJson()
    {
        $this->secondarySetUp();

        $response = $this->delete(route('delete-url', ['hashedId' => $this->url->getShortenedString()]));

        $response->assertStatus(201)->assertJson(
            [
                'data' => [
                    'url' =>$this->url->getUnfurledUrl(),
                    'shortened' => route('get-original-url', ['hashedId' => $this->url->getShortenedString()])
                ]
            ]
        );
    }

    /**
     * Checks required validator on show URL
     */
    public function testShowUrlJsonFailedValidationRequired()
    {
        $response = $this->get(route('show-url', ['hashedId' => " "]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.required', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }

    /**
     * Checks alpha num validator on show URL
     */
    public function testShowUrlJsonFailedValidationAlphaNum()
    {
        $response = $this->get(route('show-url', ['hashedId' => "*"]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.alpha_num', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }

    /**
     * Checks existence validator on show URL
     */
    public function testShowUrlJsonFailedValidationNotExists()
    {
        $response = $this->get(route('show-url', ['hashedId' => \Str::random(5)]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.exists', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }


    /**
     * Checks required validator on delete URL
     */
    public function testDeleteUrlJsonFailedValidationRequired()
    {
        $response = $this->delete(route('delete-url', ['hashedId' => " "]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.required', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }

    /**
     * Checks alpha num validator on delete URL
     */
    public function testDeleteUrlJsonFailedValidationAlphaNum()
    {
        $response = $this->delete(route('delete-url', ['hashedId' => "*"]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.alpha_num', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }

    /**
     * Checks existence validator on delete URL
     */
    public function testDeleteUrlJsonFailedValidationNotExists()
    {
        $response = $this->delete(route('delete-url', ['hashedId' => \Str::random(5)]));

        $response->assertStatus(400)->assertJson(
            [
                'errors' => [
                    'hashedId' => [
                        __('validation.exists', ['attribute' => 'hashed id'])
                    ]
                ]
            ]
        );
    }

}
