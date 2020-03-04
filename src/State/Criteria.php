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

/**
 * Represents the criteria used to query a saga state instance.
 */
class Criteria
{
    private $comparisons;

    /**
     * @param array $comparisons key => value, "column" => value
     */
    public function __construct(array $comparisons)
    {
        $this->comparisons = $comparisons;
    }

    /**
     * @return array
     */
    public function getComparisons()
    {
        return $this->comparisons;
    }
}
