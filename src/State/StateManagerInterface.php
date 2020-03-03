<?php

declare(strict_types=1);

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

/**
 * Responsible for retrieving the State object for a given criteria.
 *
 * The State object can be an existing one that was persisted, or a new
 * object if appropriate.
 */
interface StateManagerInterface
{
    /**
     * @param Criteria|null $criteria
     *
     * @return State
     */
    public function findOneBy($criteria, $sagaId);
}
