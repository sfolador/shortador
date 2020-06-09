<?php


namespace App\Listeners;


use App\Enums\EventTypeEnum;
use App\Events\UrlCreatedEvent;
use App\Events\UrlDeletedEvent;
use App\Events\UrlEventInterface;
use App\Events\UrlOpenedEvent;
use App\Models\Event\Event;
use App\Models\Stat\Stat;
use Illuminate\Events\Dispatcher;

/**
 * Class UrlEventSubscriber
 * @package App\Listeners
 */
class UrlEventSubscriber
{

    /**
     * @param UrlEventInterface $event
     * @param $type
     */
    protected function createEventWithType(UrlEventInterface $event, $type): void
    {
        $e = new Event();
        $e->url()->associate($event->getUrl());
        /** @noinspection PhpUndefinedFieldInspection */
        $e->event_type = $type;
        $e->save();
    }

    /**
     * @param UrlCreatedEvent $event
     */
    public function onUrlCreatedEvent(UrlCreatedEvent $event): void
    {
        $this->createEventWithType($event, EventTypeEnum::created());
    }

    /**
     * @param UrlOpenedEvent $event
     */
    public function onUrlOpenedEvent(UrlOpenedEvent $event): void
    {

        $this->createEventWithType($event, EventTypeEnum::opened());

        $increment = 1;
        $url = $event->getUrl();
        \Log::info("on url opened event" . $url->id);
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $stat = Stat::urlIs($url->id)->first();
        if (!$stat) {
            $stat = new Stat();
            $stat->url()->associate($url);
            $stat->opens = 0;
        }

        $stat->opens += $increment;
        $stat->save();
    }

    /**
     * @param UrlDeletedEvent $event
     */
    public function onUrlDeletedEvent(UrlDeletedEvent $event): void
    {
        $this->createEventWithType($event, EventTypeEnum::deleted());
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events): void
    {
        $events->listen(
            UrlCreatedEvent::class,
            'App\Listeners\UrlEventSubscriber@onUrlCreatedEvent'
        );

        $events->listen(
            UrlOpenedEvent::class,
            'App\Listeners\UrlEventSubscriber@onUrlOpenedEvent'
        );
        $events->listen(
            UrlDeletedEvent::class,
            'App\Listeners\UrlEventSubscriber@onUrlDeletedEvent'
        );
    }
}
