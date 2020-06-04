<?php

namespace App\Events;

use App\Models\Url\Url;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UrlCreatedEvent implements UrlEventInterface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Url $url
     */
    public $url;

    /**
     * UrlCreatedEvent constructor.
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        //
        $this->url = $url;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getUrl(): Url
    {
       return $this->url;
    }
}
