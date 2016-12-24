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

use Broadway\Saga\TestCase;

class CriteriaTest extends TestCase
{
    /**
     * @test
     */
    public function it_exposes_the_comparisons()
    {
        $data = ['appId' => 42, 'companyId' => 21];

        $criteria = new Criteria($data);

        $this->assertEquals($data, $criteria->getComparisons());
    }
}
