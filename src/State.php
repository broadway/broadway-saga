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

namespace Broadway\Saga;

use Broadway\Serializer\Serializable;

/**
 * Encapsulates the state of a saga.
 *
 * Saga's are implemented as stateless services. The state is passed to a saga
 * every time it's called. The state is also used to signal that the saga is
 * finished.
 *
 * @todo should it be immutable?
 */
class State implements Serializable
{
    private $done = false;
    private $id;
    private $values = [];

    /**
     * @param string $id Unique identifier for the state object
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if (!isset($this->values[$key])) {
            return null; // todo: exception?
        }

        return $this->values[$key];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Mark the saga as done.
     */
    public function setDone()
    {
        $this->done = true;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): array
    {
        return ['id' => $this->getId(), 'values' => $this->values, 'done' => $this->isDone()];
    }

    /**
     * {@inheritdoc}
     */
    public static function deserialize(array $data)
    {
        $state = new State($data['id']);
        $state->done = $data['done'];
        $state->values = $data['values'];

        return $state;
    }
}
