<?php

declare(strict_types=1);

namespace Broadway\Saga\Testing;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\TestCase;

class EventCollectorListenerTest extends TestCase
{

    /**
     * @test
     *
     **/
    public function popAndHandleEventsTest()
    {
        $domainMessage = DomainMessage::recordNow('id', 0, new Metadata([]), []);

        $listener = new EventCollectorListener();
        $listener->handle($domainMessage);
        $events = $listener->popEvents();
        self::assertEquals($events,[$domainMessage]);
    }
}
