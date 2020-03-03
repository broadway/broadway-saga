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

use Broadway\Saga\State\Testing\AbstractRepositoryTest;

class InMemoryRepositoryTest extends AbstractRepositoryTest
{
    protected function createRepository()
    {
        return new InMemoryRepository();
    }
}
