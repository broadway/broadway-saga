<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Saga\Metadata;

use Broadway\Saga\MetadataInterface;
use Broadway\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class StaticallyConfiguredSagaMetadataFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_metadata_using_the_saga_configuration(): void
    {
        $this->markTestSkipped('Yay phpunit');
        $metadataFactory = new StaticallyConfiguredSagaMetadataFactory();
        $criteria        = new Criteria(['id' => 'YoLo']);

        $saga = $this->getMockBuilder(StaticallyConfiguredSagaInterface::class)->getMock();
        $saga->staticExpects($this->any())
            ->method('configuration')
            ->willReturn([
                'StaticallyConfiguredSagaMetadataFactoryTestEvent' => static function ($event) use (
                    $criteria
                ){
                    return $criteria;
                },
            ]);

        $metadata = $metadataFactory->create($saga);

        $this->assertInstanceOf(MetadataInterface::class, $metadata);

        $event = new StaticallyConfiguredSagaMetadataFactoryTestEvent();
        $this->assertTrue($metadata->handles($event));
        $this->assertEquals($criteria, $metadata->criteria($event));
    }
}

class StaticallyConfiguredSagaMetadataFactoryTestEvent
{
}
