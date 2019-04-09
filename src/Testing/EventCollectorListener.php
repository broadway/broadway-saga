<?php

declare(strict_types=1);


namespace Broadway\Saga\Testing;


use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;

/**
 * Class EventCollectorListener.
 *
 * @category Tests\Unit\Infrastructure\Event
 */
class EventCollectorListener implements EventListener
{
    /**
     * @var DomainMessage[]
     */
    private $publishedEvents = [];

    /**
     * Collect new event.
     *
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage): void
    {
        $this->publishedEvents[] = $domainMessage;
    }

    /**
     * Return all events and clear storage.
     *
     * @return DomainMessage[]
     */
    public function popEvents(): array
    {
        $events = $this->publishedEvents;
        $this->publishedEvents = [];

        return $events;
    }
}
