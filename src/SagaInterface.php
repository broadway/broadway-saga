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

use Broadway\Domain\DomainMessage;

/**
 * Interface SagaInterface
 * @package Broadway\Saga
 */
interface SagaInterface
{
    /**
     * @param State $state
     * @param DomainMessage $domainMessage
     *
     * @return State
     */
    public function handle(State $state, DomainMessage $domainMessage): State;
}
