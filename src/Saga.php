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

use BadMethodCallException;
use Broadway\Saga\Metadata\CatchableSagaInterface;
use Throwable;

/**
 * Class Saga
 * @package Broadway\Saga
 */
use Broadway\Domain\DomainMessage;

abstract class Saga implements CatchableSagaInterface
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Checks whether exception is caught
     *
     * @var boolean
     */
    protected $exceptionCaught = false;

    /**
     * {@inheritDoc}
     */
    public function handle(State $state, DomainMessage $domainMessage): State
    {
        $event = $domainMessage->getPayload();
        $method = $this->getHandleMethod($event);

        if (! method_exists($this, $method)) {
            throw new BadMethodCallException(
                sprintf(
                    "No handle method '%s' for event '%s'.",
                    $method,
                    get_class($event)
                )
            );
        }
        $state->setInProgress();

        try {
            $this->exception = null;
            $state = $this->$method($state, $event, $domainMessage);
        } catch (Throwable $e) {
            $this->exception = $e;
            $state->set('exception', $e->getMessage().', class: '.$e->getFile().', line: '.$e->getLine());
            $state->setFailed();
        }

        return $state;
    }

    /**
     * @param $event
     *
     * @return string
     */
    private function getHandleMethod($event): string
    {
        $classParts = explode('\\', get_class($event));

        return 'handle' . end($classParts);
    }

    /**
     * Is was exception while handle event
     *
     * @return bool
     */
    public function isThrowException(): bool
    {
        return !$this->exception;
    }

    /**
     * Returns the exception
     *
     * @return Throwable|null
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * Indicates that exception is caught
     */
    public function catchException(): void
    {
        $this->exceptionCaught = true;
    }

    /**
     * Checks whether exception is caught
     *
     * @return boolean
     */
    public function isExceptionCaught(): bool
    {
        return $this->exceptionCaught;
    }
}
