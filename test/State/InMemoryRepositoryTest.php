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

use Broadway\Saga\State\Testing\AbstractRepositoryTest;

/**
 * Class InMemoryRepositoryTest
 * @package Broadway\Saga\State
 */
class InMemoryRepositoryTest extends AbstractRepositoryTest
{
    /**
     * @return RepositoryInterface
     */
    protected function createRepository(): RepositoryInterface
    {
        return new InMemoryRepository();
    }
}
