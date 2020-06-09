<?php


namespace App\Http\Controllers\Shortener;


use App\Events\UrlOpenedEvent;
use App\Http\Controllers\Controller;
use App\Models\Url\Url;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
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
     * @param Request $request
     * @param $hashedId
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    public function show(Request $request, $hashedId)
    {
        $v = \Validator::make(
            ['hashedId' => $hashedId],
            [
                'hashedId' => 'required|alpha_num'
            ]
        );

        if ($v->fails()) {
            return response('', 404);
        }


        $url = \Shortener::load($hashedId);


        if (!$url) {
            return response('', 404);
        }

        /** @noinspection PhpParamsInspection */
        event(new UrlOpenedEvent($url));

        return redirect()->away($url->getUnfurledUrl());
    }

}
