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

/**
 * Interface SagaInterface
 * @package Broadway\Saga
 */
interface SagaInterface
{
    /**
     * @param mixed $event
     *
     * @param State $state
     *
     * @return State
     */
    public function handle($event, State $state): State;
}
