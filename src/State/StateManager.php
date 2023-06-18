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
use Broadway\UuidGenerator\UuidGeneratorInterface;

class StateManager implements StateManagerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var UuidGeneratorInterface
     */
    private $generator;

    public function __construct(RepositoryInterface $repository, UuidGeneratorInterface $generator)
    {
        $this->repository = $repository;
        $this->generator = $generator;
    }

    public function findOneBy(?Criteria $criteria, string $sagaId): ?State
    {
        // TODO: Use CreationPolicy to determine whether and how a new state should be created
        if ($criteria instanceof Criteria) {
            return $this->repository->findOneBy($criteria, $sagaId);
        }

        return new State($this->generator->generate());
    }
}
