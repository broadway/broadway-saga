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

/**
 * Class InMemoryRepository
 * @package Broadway\Saga\State
 */
class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var mixed[]
     */
    private $states = [];

    /**
     * {@inheritDoc}
     */
    public function findOneBy(Criteria $criteria, $sagaId): ?State
    {
        if (! isset($this->states[$sagaId])) {
            return null;
        }

        $states = $this->states[$sagaId];

        foreach ($criteria->getComparisons() as $key => $value) {
            $states = array_filter($states, static function (State $elem) use ($key, $value) {
                $stateValue = $elem->get($key);

                return is_array($stateValue) ? in_array($value, $stateValue, true) : $value === $stateValue;
            });
        }

        $amount = count($states);

        if (1 === $amount) {
            return current($states);
        }

        if ($amount > 1) {
            throw new RepositoryException('Multiple saga state instances found.');
        }

        return null;
    }

    /**
     * Find failed saga states
     *
     * @param Criteria|null $criteria
     * @param string|null $sagaId
     *
     * @return array
     */
    public function findFailed(?Criteria $criteria = null, ?string $sagaId = null): array
    {
        if (null !== $sagaId) {
            if (! isset($this->states[$sagaId])) {
                return null;
            }

            $states = $this->states[$sagaId];
        } else {
            $states = [];

            foreach ($this->states as $sagaStates) {
                $states = $states + $sagaStates;
            }
        }

        $failedStates = [];

        foreach ($states as $state) {
            /** @var State $state */
            if ($state->isFailed()) {
                $failedStates[] = $state;
            }
        }

        if (null === $criteria) {
            return $failedStates;
        }

        foreach ($criteria->getComparisons() as $key => $value) {
            $failedStates = array_filter($failedStates, static function ($elem) use ($key, $value) {
                $stateValue = $elem->get($key);

                return is_array($stateValue) ? in_array($value, $stateValue) : $value === $stateValue;
            });
        }

        return $failedStates;
    }

    /**
     * {@inheritDoc}
     */
    public function save(State $state)
    {
        if ($state->isDone()) {
            unset($this->states[$state->getSagaId()][$state->getId()]);
        } else {
            $this->states[$state->getSagaId()][$state->getId()] = $state;
        }
    }
}
