<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Saga;

use Assert\Assertion as Assert;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventDispatcher\Testing\TraceableEventDispatcher;
use Broadway\Saga\Metadata\StaticallyConfiguredSagaInterface;
use Broadway\Saga\Metadata\StaticallyConfiguredSagaMetadataFactory;
use Broadway\Saga\State\Criteria;
use Broadway\Saga\State\InMemoryRepository;
use Broadway\Saga\State\StateManager;
use Broadway\Saga\Testing\TraceableSagaStateRepository;
use Broadway\UuidGenerator\Rfc4122\Version4Generator;
use PHPUnit\Framework\TestCase;

/**
 * Class MultipleSagaManagerTest
 * @package Broadway\Saga
 */
class MultipleSagaManagerTest extends TestCase
{
    /**
     * @var MultipleSagaManager
     */
    private $manager;
    /**
     * @var TraceableSagaStateRepository
     */
    private $repository;
    /**
     * @var mixed[]
     */
    private $sagas;

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @var StaticallyConfiguredSagaMetadataFactory
     */
    private $metadataFactory;

    /**
     * @var TraceableEventDispatcher
     */
    private $eventDispatcher;

    /**
     *
     */
    public function setUp()
    {
        $this->repository      = new TraceableSagaStateRepository(new InMemoryRepository());
        $this->sagas           = ['sagaId' => new SagaManagerTestSaga()];
        $this->stateManager    = new StateManager($this->repository, new Version4Generator());
        $this->metadataFactory = new StaticallyConfiguredSagaMetadataFactory();
        $this->eventDispatcher = new TraceableEventDispatcher();
        $this->manager         = $this->createManager($this->repository, $this->sagas, $this->stateManager, $this->metadataFactory, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function it_saves_the_modified_state(): void
    {
        $s1 = new State(1, 'sagaId');
        $s1->set('appId', 42);
        $this->repository->save($s1);
        $this->repository->trace();

        $this->handleEvent($this->manager, new TestEvent1());

        $saved = $this->repository->getSaved();
        $this->assertCount(1, $saved);
        $this->assertEquals(1, $saved[0]->getId());
        $this->assertEquals('testevent1', $saved[0]->get('event'));
    }

    /**
     * @test
     */
    public function it_removes_the_state_if_the_saga_is_done(): void
    {
        $s1 = new State(1, 'sagaId');
        $s1->set('appId', 42);
        $this->repository->save($s1);
        $this->repository->trace();

        $this->handleEvent($this->manager, new TestEventDone());

        $removed = $this->repository->getRemoved();
        $this->assertCount(1, $removed);
        $this->assertEquals(1, $removed[0]->getId());
    }

    /**
     * @test
     */
    public function it_creates_and_passes_a_new_saga_state_instance_if_no_criteria_is_configured(): void
    {
        $this->repository->trace();
        $this->handleEvent($this->manager, new TestEvent2());

        $saved = $this->repository->getSaved();
        $this->assertCount(1, $saved);
        $this->assertEquals('testevent2', $saved[0]->get('event'));
    }

    /**
     * @test
     */
    public function it_does_not_call_the_saga_if_it_is_not_configured_to_handle_an_event(): void
    {
        foreach ($this->sagas as $saga) {
            $this->assertFalse($saga->isCalled);
        }

        $this->handleEvent($this->manager, new TestEvent3());

        foreach ($this->sagas as $saga) {
            $this->assertFalse($saga->isCalled);
        }
    }

    /**
     * @test
     */
    public function it_does_not_call_the_saga_when_no_state_is_found(): void
    {
        foreach ($this->sagas as $saga) {
            $this->assertFalse($saga->isCalled);
        }

        $this->handleEvent($this->manager, new TestEvent1());

        foreach ($this->sagas as $saga) {
            $this->assertFalse($saga->isCalled);
        }
    }

    /**
     * @test
     */
    public function it_calls_all_sagas_configured_for_that_event(): void
    {
        $sagas   = [new SagaManagerTestSaga(), new SagaManagerTestSaga()];
        $manager = $this->createManager($this->repository, $sagas, $this->stateManager, $this->metadataFactory, $this->eventDispatcher);

        foreach ($sagas as $saga) {
            $this->assertFalse($saga->isCalled);
        }

        $this->handleEvent($manager, new TestEvent2());

        foreach ($sagas as $saga) {
            $this->assertTrue($saga->isCalled);
        }
    }

    /**
     * @test
     */
    public function it_calls_all_sagas_configured_for_that_event_even_when_a_state_is_not_found_for_previous_saga(): void
    {
        $s1 = new State(1, 'sagaId');
        $s1->set('appId', 42);
        $this->repository->save($s1);

        $sagas   = ['saga1' => new SagaManagerTestSaga(), 'saga2' => new SagaManagerTestSaga()];
        $manager = $this->createManager($this->repository, $sagas, $this->stateManager, $this->metadataFactory, $this->eventDispatcher);

        $this->assertFalse($sagas['saga2']->isCalled);

        $this->handleEvent($manager, new TestEvent1());

        $this->assertTrue($sagas['saga2']->isCalled);
    }

    /**
     * @test
     */
    public function it_gives_every_saga_an_own_stage_even_when_the_criteria_are_the_same(): void
    {
        $s1 = new State(1, 'saga1');
        $s1->set('appId', 42);
        $this->repository->save($s1);
        $s2 = new State(2, 'saga2');
        $s2->set('appId', 42);
        $this->repository->save($s2);

        $sagas   = ['saga1' => new SagaManagerTestSaga(), 'saga2' => new SagaManagerTestSaga()];
        $manager = $this->createManager($this->repository, $sagas, $this->stateManager, $this->metadataFactory, $this->eventDispatcher);

        $this->repository->trace();

        $this->handleEvent($manager, new TestEvent1());

        $saved = $this->repository->getSaved();
        $this->assertCount(2, $saved);
        $this->assertEquals('testevent1', $saved[0]->get('event'));
        $this->assertEquals('testevent1', $saved[1]->get('event'));
    }

    /**
     * @test
     */
    public function it_dispatches_events(): void
    {
        $stateId = 1;
        $s1      = new State($stateId, 'sagaId');
        $s1->set('appId', 42);
        $this->repository->save($s1);
        $this->handleEvent($this->manager, new TestEvent1());

        $dispatchedEvents = $this->eventDispatcher->getDispatchedEvents();
        $this->assertCount(2, $dispatchedEvents);

        $this->assertEquals('broadway.saga.pre_handle', $dispatchedEvents[0]['event']);
        $this->assertEquals('sagaId', $dispatchedEvents[0]['arguments'][0]);
        $this->assertEquals($stateId, $dispatchedEvents[0]['arguments'][1]);

        $this->assertEquals('broadway.saga.post_handle', $dispatchedEvents[1]['event']);
        $this->assertEquals('sagaId', $dispatchedEvents[1]['arguments'][0]);
        $this->assertEquals($stateId, $dispatchedEvents[1]['arguments'][1]);
    }

    /**
     * @test
     */
    public function it_dispatches_events_when_no_state_is_found(): void
    {
        $this->handleEvent($this->manager, new TestEvent2());

        $dispatchedEvents = $this->eventDispatcher->getDispatchedEvents();
        $this->assertCount(2, $dispatchedEvents);

        $this->assertEquals('broadway.saga.pre_handle', $dispatchedEvents[0]['event']);
        $this->assertEquals('sagaId', $dispatchedEvents[0]['arguments'][0]);
        Assert::uuid($dispatchedEvents[0]['arguments'][1]);

        $this->assertEquals('broadway.saga.post_handle', $dispatchedEvents[1]['event']);
        $this->assertEquals('sagaId', $dispatchedEvents[1]['arguments'][0]);
        Assert::uuid($dispatchedEvents[1]['arguments'][1]);
        $this->assertEquals($dispatchedEvents[0]['arguments'][1], $dispatchedEvents[1]['arguments'][1]);
    }

    /**
     * @test
     */
    public function it_does_not_dispatch_an_event_when_no_saga_is_called(): void
    {
        $this->handleEvent($this->manager, new TestEvent1());

        $dispatchedEvents = $this->eventDispatcher->getDispatchedEvents();
        $this->assertCount(0, $dispatchedEvents);
    }

    /**
     * @param TraceableSagaStateRepository $repository
     * @param array $sagas
     * @param StateManager $stateManager
     * @param StaticallyConfiguredSagaMetadataFactory $metadataFactory
     * @param TraceableEventDispatcher $dispatcher
     *
     * @return MultipleSagaManager
     */
    private function createManager(TraceableSagaStateRepository $repository, array $sagas, StateManager $stateManager, StaticallyConfiguredSagaMetadataFactory $metadataFactory, TraceableEventDispatcher $dispatcher): MultipleSagaManager
    {
        return new MultipleSagaManager($repository, $sagas, $stateManager, $metadataFactory, $dispatcher);
    }

    /**
     * @param MultipleSagaManager $manager
     * @param $event
     */
    private function handleEvent(MultipleSagaManager $manager, $event): void
    {
        $manager->handle(DomainMessage::recordNow(1, 0, new Metadata([]), $event));
    }
}

/**
 * Class SagaManagerTestSaga
 * @package Broadway\Saga
 */
class SagaManagerTestSaga implements StaticallyConfiguredSagaInterface
{
    /**
     * @var bool
     */
    public $isCalled = false;

    /**
     * @param State|null $state
     * @param DomainMessage $domainMessage
     *
     * @return State
     */
    public function handle(State $state, DomainMessage $domainMessage): State
    {
        $this->isCalled = true;
        $event = $domainMessage->getPayload();

        if ($event instanceof TestEvent1) {
            $state->set('event', 'testevent1');
        } elseif ($event instanceof TestEvent2) {
            $state->set('event', 'testevent2');
        } elseif ($event instanceof TestEventDone) {
            $state->setDone();
        }

        return $state;
    }

    /**
     * @return array
     */
    public static function configuration(): array
    {
        return [
            'TestEvent1'    => static function (){
                return new Criteria(['appId' => 42]);
            },
            'TestEvent2'    => function (){
            },
            'TestEventDone' => static function (){
                return new Criteria(['appId' => 42]);
            },
        ];
    }
}

/**
 * Class TestEvent1
 * @package Broadway\Saga
 */
class TestEvent1
{
}

/**
 * Class TestEvent2
 * @package Broadway\Saga
 */
class TestEvent2
{
}

/**
 * Class TestEvent3
 * @package Broadway\Saga
 */
class TestEvent3
{
}

/**
 * Class TestEventDone
 * @package Broadway\Saga
 */
class TestEventDone
{
}
