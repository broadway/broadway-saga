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

use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\Testing\TraceableCommandBus;
use Broadway\EventDispatcher\CallableEventDispatcher;
use Broadway\Saga\Metadata\StaticallyConfiguredSagaMetadataFactory;
use Broadway\Saga\MultipleSagaManager;
use Broadway\Saga\SagaInterface;
use Broadway\Saga\State\InMemoryRepository;
use Broadway\Saga\State\StateManager;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use PHPUnit\Framework\TestCase;

abstract class SagaScenarioTestCase extends TestCase
{
    /**
     * @var Scenario
     */
    protected $scenario;

    /**
     * Create the saga you want to test in this test case
     *
     * @param  CommandBus $commandBus
     *
     * @return SagaInterface
     */
    abstract protected function createSaga(CommandBus $commandBus);

    protected function setUp(): void
    {
        parent::setUp();

        $this->scenario = $this->createScenario();
    }

    protected function createScenario()
    {
        $traceableCommandBus = new TraceableCommandBus();
        $saga                = $this->createSaga($traceableCommandBus);
        $sagaStateRepository = new InMemoryRepository();
        $sagaManager         = new MultipleSagaManager(
            $sagaStateRepository,
            [$saga],
            new StateManager($sagaStateRepository, new Version4Generator()),
            new StaticallyConfiguredSagaMetadataFactory(),
            new CallableEventDispatcher()
        );

        return new Scenario($this, $sagaManager, $traceableCommandBus);
    }
}
