<?php

declare(strict_types=1);

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) 2020 Broadway project
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

class Scenario
{
    private $testCase;
    private $sagaManager;
    private $traceableCommandBus;
    private $aggregateId;
    private $playhead;

    public function __construct(
        TestCase $testCase,
        MultipleSagaManager $sagaManager,
        TraceableCommandBus $traceableCommandBus
    ) {
        $this->testCase = $testCase;
        $this->sagaManager = $sagaManager;
        $this->traceableCommandBus = $traceableCommandBus;
        $this->aggregateId = 1;
        $this->playhead = -1;
    }

    /**
     * @param string $aggregateId
     *
     * @return Scenario
     */
    public function withAggregateId($aggregateId)
    {
        $this->aggregateId = $aggregateId;

        return $this;
    }

    /**
     * @return Scenario
     */
    public function given(array $events = [])
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
    public function when($event)
    {
        $this->traceableCommandBus->record();

        $this->sagaManager->handle($this->createDomainMessageForEvent($event));

        return $this;
    }

    /**
     * @return Scenario
     */
    public function then(array $commands)
    {
        $this->testCase->assertEquals($commands, $this->traceableCommandBus->getRecordedCommands());

        return $this;
    }

    private function createDomainMessageForEvent($event)
    {
        ++$this->playhead;

        return DomainMessage::recordNow($this->aggregateId, $this->playhead, new Metadata([]), $event);
    }
}
