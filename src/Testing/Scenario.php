<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Saga\Testing;

use Broadway\CommandHandling\Testing\TraceableCommandBus;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\Saga\MultipleSagaManager;
use PHPUnit\Framework\TestCase;

/**
 * Class Scenario
 * @package Broadway\Saga\Testing
 */
class Scenario
{
    /**
     * @var TestCase
     */
    private $testCase;

    /**
     * @var MultipleSagaManager
     */
    private $sagaManager;

    /**
     * @var TraceableCommandBus
     */
    private $traceableCommandBus;

    /**
     * @var int
     */
    private $playhead;

    /**
     * Scenario constructor.
     *
     * @param TestCase $testCase
     * @param MultipleSagaManager $sagaManager
     * @param TraceableCommandBus $traceableCommandBus
     */
    public function __construct(
        TestCase $testCase,
        MultipleSagaManager $sagaManager,
        TraceableCommandBus $traceableCommandBus
    ) {
        $this->testCase            = $testCase;
        $this->sagaManager         = $sagaManager;
        $this->traceableCommandBus = $traceableCommandBus;
        $this->playhead            = -1;
    }

    /**
     * @param mixed[] $events
     *
     * @return Scenario
     */
    public function given(array $events = []): self
    {
        foreach ($events as $given) {
            $this->sagaManager->handle($this->createDomainMessageForEvent($given));
        }

        return $this;
    }

    /**
     * @param mixed $event
     *
     * @return Scenario
     */
    public function when($event): self
    {
        $this->traceableCommandBus->record();

        $this->sagaManager->handle($this->createDomainMessageForEvent($event));

        return $this;
    }

    /**
     * @param mixed[] $commands
     *
     * @return Scenario
     */
    public function then(array $commands): self
    {
        $this->testCase::assertEquals($commands, $this->traceableCommandBus->getRecordedCommands());

        return $this;
    }

    /**
     * @param $event
     *
     * @return DomainMessage
     */
    private function createDomainMessageForEvent($event): DomainMessage
    {
        $this->playhead++;

        return DomainMessage::recordNow(1, $this->playhead, new Metadata([]), $event);
    }
}
