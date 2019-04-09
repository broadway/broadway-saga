<?php

declare(strict_types=1);


namespace Broadway\Saga\Testing;

use Broadway\Saga\State;
use Broadway\Saga\State\Criteria;
use Broadway\Saga\State\RepositoryInterface;

/**
 * Class TraceableSagaStateRepository
 * @package Broadway\Saga\Testing
 */
class TraceableSagaStateRepository implements RepositoryInterface
{
    /**
     * @var bool
     */
    private $tracing = false;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var mixed[]
     */
    private $saved   = [];

    /**
     * @var mixed[]
     */
    private $removed = [];

    /**
     * TraceableSagaStateRepository constructor.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Criteria $criteria
     * @param $sagaId
     *
     * @return State|null
     */
    public function findOneBy(Criteria $criteria, $sagaId): ?State
    {
        return $this->repository->findOneBy($criteria, $sagaId);
    }

    /**
     * @return mixed[]
     */
    public function getSaved(): array
    {
        return $this->saved;
    }

    /**
     * @param State $state
     * @param $sagaId
     */
    public function save(State $state, $sagaId)
    {
        $this->repository->save($state, $sagaId);

        if ($this->tracing) {
            if ($state->isDone()) {
                $this->removed[] = $state;
            } else {
                $this->saved[] = $state;
            }
        }
    }

    /**
     *
     */
    public function trace(): void
    {
        $this->tracing = true;
    }

    /**
     * @return mixed[]
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }
}
