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

namespace Broadway\Saga\State\Testing;

use Broadway\Saga\State;
use Broadway\Saga\State\Criteria;
use Broadway\Saga\State\RepositoryInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractRepositoryTest extends TestCase
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function setUp(): void
    {
        $this->repository = $this->createRepository();
    }

    abstract protected function createRepository(): RepositoryInterface;

    /**
     * @test
     */
    public function it_saves_a_state(): void
    {
        $s1 = new State('1');
        $s1->set('appId', 42);
        $this->repository->save($s1, 'sagaId');

        $found = $this->repository->findOneBy(new Criteria(['appId' => 42]), 'sagaId');

        $this->assertEquals($s1, $found);
    }

    /**
     * @test
     */
    public function it_removes_a_state_when_state_is_done(): void
    {
        $s1 = new State('1');
        $s1->set('appId', 42);
        $this->repository->save($s1, 'sagaId');
        $criteria = new Criteria(['appId' => 42]);

        $found = $this->repository->findOneBy($criteria, 'sagaId');
        $this->assertEquals($s1, $found);

        $s1->setDone();
        $this->repository->save($s1, 'sagaId');
        $this->assertNull($this->repository->findOneBy($criteria, 'sagaId'));
    }

    /**
     * @test
     */
    public function it_finds_documents_matching_criteria(): void
    {
        $state = new State('yolo');
        $state->set('Hi', 'There');
        $state->set('Bye', 'bye');
        $state->set('You', 'me');
        $this->repository->save($state, 'sagaId');
        $fetchedState = $this->repository->findOneBy(new Criteria(['Hi' => 'There', 'Bye' => 'bye']), 'sagaId');
        $this->assertEquals($state, $fetchedState);
    }

    /**
     * @test
     */
    public function it_finds_documents_matching_in_criteria(): void
    {
        $state = new State('yolo');
        $state->set('Hi', ['There', 'You']);
        $state->set('Bye', 'bye');
        $state->set('You', 'me');
        $this->repository->save($state, 'sagaId');
        $fetchedState = $this->repository->findOneBy(new Criteria(['Hi' => 'There', 'Bye' => 'bye']), 'sagaId');
        $this->assertEquals($state, $fetchedState);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_states_match_the_criteria(): void
    {
        $state = new State('yolo');
        $state->set('Hi', 'There');
        $this->repository->save($state, 'sagaId');
        $this->assertNull($this->repository->findOneBy(new Criteria(['Bye' => 'There']), 'sagaId'));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_multiple_matching_elements_are_found(): void
    {
        $this->expectException('Broadway\Saga\State\RepositoryException');
        $this->expectExceptionMessage('Multiple saga state instances found.');
        $s1 = new State('1');
        $s1->set('appId', 42);
        $this->repository->save($s1, 'sagaId');
        $s2 = new State('2');
        $s2->set('appId', 42);
        $this->repository->save($s2, 'sagaId');

        $this->repository->findOneBy(new Criteria(['appId' => 42]), 'sagaId');
    }

    /**
     * @test
     */
    public function saving_a_state_object_with_the_same_id_only_keeps_the_last_one(): void
    {
        $s1 = new State('31415');
        $s1->set('appId', 42);
        $this->repository->save($s1, 'sagaId');
        $s2 = new State('31415');
        $s2->set('appId', 1337);
        $this->repository->save($s2, 'sagaId');

        $found = $this->repository->findOneBy(new Criteria(['appId' => 1337]), 'sagaId');
        $this->assertInstanceOf(State::class, $found);

        $this->assertEquals(31415, $found->getId());
    }
}
