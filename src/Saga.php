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

abstract class Saga implements SagaInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event, State $state)
    {
        $method = $this->getHandleMethod($event);

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf("No handle method '%s' for event '%s'.", $method, get_class($event)));
        }

        return $this->$method($event, $state);
    }

    private function getHandleMethod($event)
    {
        $classParts = explode('\\', get_class($event));

        return 'handle'.end($classParts);
    }
}
