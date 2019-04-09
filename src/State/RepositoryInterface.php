<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Saga\State;

use Broadway\Saga\State;

interface RepositoryInterface
{
    /**
     * @param Criteria $criteria
     * @param string $sagaId
     *
     * @return State|null ?State
     *
     * @todo specific exception
     */
    public function findOneBy(Criteria $criteria, $sagaId): ?State;

    /**
     * @param State $state
     * @param $sagaId
     *
     * @return mixed
     */
    public function save(State $state, $sagaId);
}
