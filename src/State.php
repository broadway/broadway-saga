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
    public const SAGA_STATE_STATUS_IN_PROGRESS  = 0;
    public const SAGA_STATE_STATUS_DONE         = 1;
    public const SAGA_STATE_STATUS_FAILED       = 2;
    public const SAGA_STATE_STATUS_DIED         = 3;

    /**
     * Saga type id
     *
     * @var string
     */
    private $sagaId;

    /**
     * Saga state process status
     *
     * @var int
     */
    private $status;

    /**
     * Saga process unique id
     *
     * @var string
     */
    private $id;

    /**
     * Saga state values
     *
     * @var array
     */
    private $values = [];

    /**
     * Is new state
     * @var bool
     */
    private $isNew;

    /**
     * @param string $id Unique identifier for the state object
     * @param string $sagaId
     * @param bool $isNew
     */
    public function __construct($id, string $sagaId, bool $isNew = true)
    {
        $this->id = $id;
        $this->sagaId = $sagaId;
        $this->isNew = $isNew;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value): self
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if (! isset($this->values[$key])) {
            return null; // todo: exception?
        }

        return $this->values[$key];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSagaId(): string
    {
        return $this->sagaId;
    }

    /**
     * Mark the saga as done.
     *
     * @return $this
     */
    public function setDone(): self
    {
        $this->status = self::SAGA_STATE_STATUS_DONE;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDone(): bool
    {
        return $this->status === self::SAGA_STATE_STATUS_DONE;
    }

    /**
     * Mark the saga as failed.
     *
     * @return $this
     */
    public function setFailed(): self
    {
        $this->status = self::SAGA_STATE_STATUS_FAILED;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFailed(): bool
    {
        return $this->status === self::SAGA_STATE_STATUS_FAILED;
    }

    /**
     * Mark the saga as died.
     *
     * @return $this
     */
    public function setDied(): self
    {
        $this->status = self::SAGA_STATE_STATUS_DIED;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDied()
    {
        return $this->status === self::SAGA_STATE_STATUS_DIED;
    }

    /**
     * Mark the saga as failed.
     */
    public function setInProgress(): self
    {
        $this->status = self::SAGA_STATE_STATUS_IN_PROGRESS;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInProgress(): bool
    {
        return $this->status === self::SAGA_STATE_STATUS_IN_PROGRESS;
    }

    /**
     * Return Status
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Is state new
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * Set `new` flag.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setNewFlag(bool $flag): self
    {
        $this->isNew = $flag;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(): array
    {
        return ['id' => $this->getId(), 'values' => $this->values, 'status' => $this->getStatus(), 'saga_id' => $this->getSagaId()];
    }

    /**
     * {@inheritDoc}
     */
    public static function deserialize(array $data)
    {
        $state          = new State($data['id'], $data['saga_id']);
        $state->status  = $data['status'];
        $state->values  = $data['values'];

        return $state;
    }
}
