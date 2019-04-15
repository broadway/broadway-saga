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

/**
 * Class StaticallyConfiguredSagaMetadataFactoryTest
 * @package Broadway\Saga\Metadata
 */
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

/**
 * Class StaticallyConfiguredSagaMetadataFactoryTestEvent
 * @package Broadway\Saga\Metadata
 */
class StaticallyConfiguredSagaMetadataFactoryTestEvent
{
}

/**
 * Class StaticallyConfiguredSaga
 * @package Broadway\Saga\Metadata
 */
class StaticallyConfiguredSaga implements StaticallyConfiguredSagaInterface
{
    /**
     * @param State $state
     * @param DomainMessage $domainMessage
     *
     * @return State
     */
    public function handle(State $state, DomainMessage $domainMessage): State
    {
        return $state;
    }

    /**
     * @return array
     */
    public static function configuration(): array
    {
        return [
            'StaticallyConfiguredSagaMetadataFactoryTestEvent' => static function ($event, DomainMessage $domainMessage) {
                return new Criteria(['id' => $domainMessage->getId()]);
            }
        ];
    }
}
