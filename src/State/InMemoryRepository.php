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

class InMemoryRepository implements RepositoryInterface
{
    /**
     * @var array
     */
    private $states = [];

    public function findOneBy(Criteria $criteria, string $sagaId): ?State
    {
        if (!isset($this->states[$sagaId])) {
            return null;
        }

        $states = $this->states[$sagaId];

        foreach ($criteria->getComparisons() as $key => $value) {
            $states = array_filter($states, function ($elem) use ($key, $value): bool {
                $stateValue = $elem->get($key);

                return is_array($stateValue) ? in_array($value, $stateValue) : $value === $stateValue;
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

    public function save(State $state, string $sagaId): void
    {
        if ($state->isDone()) {
            unset($this->states[$sagaId][$state->getId()]);
        } else {
            $this->states[$sagaId][$state->getId()] = $state;
        }
    }
}
