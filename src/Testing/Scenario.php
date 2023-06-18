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
     * @var string
     */
    private $aggregateId;

    /**
     * @var int
     */
    private $playhead;

    public function __construct(
        TestCase $testCase,
        MultipleSagaManager $sagaManager,
        TraceableCommandBus $traceableCommandBus
    ) {
        $this->testCase = $testCase;
        $this->sagaManager = $sagaManager;
        $this->traceableCommandBus = $traceableCommandBus;
        $this->aggregateId = '1';
        $this->playhead = -1;
    }

    public function withAggregateId(string $aggregateId): Scenario
    {
        $this->aggregateId = $aggregateId;

        return $this;
    }

    public function given(array $events = []): Scenario
    {
        foreach ($events as $given) {
            $this->sagaManager->handle($this->createDomainMessageForEvent($given));
        }

        return $this;
    }

    public function when($event): Scenario
    {
        $this->traceableCommandBus->record();

        $this->sagaManager->handle($this->createDomainMessageForEvent($event));

        return $this;
    }

    public function then(array $commands): Scenario
    {
        $this->testCase->assertEquals($commands, $this->traceableCommandBus->getRecordedCommands());

        return $this;
    }

    private function createDomainMessageForEvent($event): DomainMessage
    {
        ++$this->playhead;

        return DomainMessage::recordNow($this->aggregateId, $this->playhead, new Metadata([]), $event);
    }
}
