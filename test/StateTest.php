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
        $this->state = new State(42);
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
    public function it_can_be_serialized_and_deserialized_to_itself(): void
    {
        $this->state->set('foo', 'bar');
        $state = State::deserialize($this->state->serialize());

        $this->assertEquals($this->state, $state);

        $this->state->setDone();

        $state = State::deserialize($this->state->serialize());
        $this->assertEquals($this->state, $state);
    }
}
