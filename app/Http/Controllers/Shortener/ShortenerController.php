<?php


namespace App\Http\Controllers\Shortener;


use App\Events\UrlOpenedEvent;
use App\Http\Controllers\Controller;
use App\Models\Url\Url;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ShortenerController
 * @package App\Http\Controllers\Shortener
 */
class ShortenerController extends Controller
{
    /**
     *
     * Opens the unfurled Url (redirects the user to the original Url)
     *
     * @param Request $request
     * @param $hashedId
     * @return Application|ResponseFactory|JsonResponse|RedirectResponse|Response
     */
    public function show(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num|exists:urls,shortened'
            ]
        );

        if ($v->fails()) {
            return response(view('errors.404', ['message' => "The Hash Id is invalid"]), 404);
        }


        $url = \Shortener::load($hashedId);

        if (!$url) {
            return response(view('errors.404', ['message' => "The Hash Id is invalid"]), 404);
        }

        /** @noinspection PhpParamsInspection */
        // triggers an Event, in order to store it
        event(new UrlOpenedEvent($url));

        return redirect()->away($url->getUnfurledUrl());
    }

}
