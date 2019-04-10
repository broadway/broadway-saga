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

use Broadway\Domain\DomainMessage;
use Broadway\Saga\MetadataInterface;
use Broadway\Saga\State;
use Broadway\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class StaticallyConfiguredSagaMetadataFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_metadata_using_the_saga_configuration(): void
    {
        $metadataFactory = new StaticallyConfiguredSagaMetadataFactory();
        $saga = new StaticallyConfiguredSaga();
        $metadata = $metadataFactory->create($saga);

        $this->assertInstanceOf(MetadataInterface::class, $metadata);

        $event = new StaticallyConfiguredSagaMetadataFactoryTestEvent();
        $domainMessage = DomainMessage::recordNow('id', 0, new \Broadway\Domain\Metadata([]), $event);

        $this->assertTrue($metadata->handles($domainMessage));
        $this->assertEquals(
            new Criteria(['id' => $domainMessage->getId()]),
            $metadata->criteria($domainMessage)
        );
    }
}

class StaticallyConfiguredSagaMetadataFactoryTestEvent
{
}

class StaticallyConfiguredSaga implements StaticallyConfiguredSagaInterface
{
    public function handle(State $state, DomainMessage $domainMessage): State
    {
        return $state;
    }

    public static function configuration()
    {
        return [
            'StaticallyConfiguredSagaMetadataFactoryTestEvent' => function ($event, DomainMessage $domainMessage) {
                return new Criteria(['id' => $domainMessage->getId()]);
            }
        ];
    }
}
