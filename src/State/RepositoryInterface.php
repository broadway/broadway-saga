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

namespace Broadway\Saga\State;

use Broadway\Saga\State;

interface RepositoryInterface
{
    /**
     * @throws RepositoryException if 0 or > 1 found
     *
     * @todo specific exception
     */
    public function findOneBy(Criteria $criteria, string $sagaId): ?State;

    public function save(State $state, string $sagaId): void;
}
