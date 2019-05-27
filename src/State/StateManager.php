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
use Broadway\UuidGenerator\UuidGeneratorInterface;

/**
 * Class StateManager
 * @package Broadway\Saga\State
 */
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

    /**
     * StateManager constructor.
     *
     * @param RepositoryInterface $repository
     * @param UuidGeneratorInterface $generator
     */
    public function __construct(RepositoryInterface $repository, UuidGeneratorInterface $generator)
    {
        $this->repository = $repository;
        $this->generator  = $generator;
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy($criteria, $sagaId): ?State
    {
        // TODO: Use CreationPolicy to determine whether and how a new state should be created
        if ($criteria instanceof Criteria) {
            return $this->repository->findOneBy($criteria, $sagaId, false);
        }

        return new State($this->generator->generate(), $sagaId);
    }
}
