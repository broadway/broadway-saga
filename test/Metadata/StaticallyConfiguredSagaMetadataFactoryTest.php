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

namespace Broadway\Saga\Metadata;

use Broadway\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class StaticallyConfiguredSagaMetadataFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_metadata_using_the_saga_configuration()
    {
        $this->markTestSkipped('Yay phpunit');
        $metadataFactory = new StaticallyConfiguredSagaMetadataFactory();
        $criteria = new Criteria(['id' => 'YoLo']);

        $saga = $this->getMockBuilder('Broadway\Saga\Metadata\StaticallyConfiguredSagaInterface')->getMock();
        $saga->staticExpects($this->any())
            ->method('configuration')
            ->will($this->returnValue(['StaticallyConfiguredSagaMetadataFactoryTestEvent' => function ($event) use ($criteria) { return $criteria; }]));

        $metadata = $metadataFactory->create($saga);

        $this->assertInstanceOf('Broadway\Saga\MetadataInterface', $metadata);

        $event = new StaticallyConfiguredSagaMetadataFactoryTestEvent();
        $this->assertTrue($metadata->handles($event));
        $this->assertEquals($criteria, $metadata->criteria($event));
    }
}

class StaticallyConfiguredSagaMetadataFactoryTestEvent
{
}
