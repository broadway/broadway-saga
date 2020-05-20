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

use PHPUnit\Framework\TestCase;

/**
 * Class StateTest
 * @package Broadway\Saga
 */
class StateTest extends TestCase
{
    /**
     * @var State
     */
    private $state;

    /**
     *
     */
    public function setUp()
    {
        $this->state = new State(42, 'test.saga', false);
    }

    /**
     * @test
     */
    public function it_returns_null_if_key_is_not_set(): void
    {
        $this->assertNull($this->state->get('foo'));
    }

    /**
     * @test
     */
    public function it_can_set_a_value(): void
    {
        $this->state->set('foo', 'bar');

        $this->assertEquals('bar', $this->state->get('foo'));
    }

    /**
     * @test
     */
    public function it_is_not_done_by_default(): void
    {
        $this->assertFalse($this->state->isDone());
    }

    /**
     * @test
     */
    public function it_can_be_set_as_done(): void
    {
        $this->state->setDone();

        $this->assertTrue($this->state->isDone());
    }

    /**
     * @test
     */
    public function it_can_be_set_as_in_progress(): void
    {
        $this->state->setInProgress();

        $this->assertTrue($this->state->isInProgress());
    }

    /**
     * @test
     */
    public function it_can_be_set_as_is_failed(): void
    {
        $this->state->setFailed();

        $this->assertTrue($this->state->isFailed());
    }

    /**
     * @test
     */
    public function it_can_be_set_as_is_died(): void
    {
        $this->state->setDied();

        $this->assertTrue($this->state->isDied());
    }

    /**
     * @test
     */
    public function a_previously_set_value_is_overridden(): void
    {
        $this->state->set('foo', 'bar');
        $this->state->set('foo', 'qux');

        $this->assertEquals('qux', $this->state->get('foo'));
    }

    /**
     * @test
     */
    public function it_exposes_its_id(): void
    {
        $this->assertEquals(42, $this->state->getId());
    }

    /**
     * @test
     */
    public function it_exposes_its_saga_id(): void
    {
        $this->assertEquals('test.saga', $this->state->getSagaId());
    }

    /**
     * @test
     */
    public function it_can_be_serialized_and_deserialized_to_itself(): void
    {
        $this->state->set('foo', 'bar');
        $this->state->setInProgress();
        $state = State::deserialize($this->state->serialize());

        $this->assertEquals($this->state, $state);

        $this->state->setDone();

        $state = State::deserialize($this->state->serialize());
        $this->assertEquals($this->state, $state);
    }
}
