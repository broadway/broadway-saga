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

interface SagaInterface
{
    /**
     * @param mixed $event
     *
     * @return State
     */
    public function handle($event, State $state);
}
