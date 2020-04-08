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

use Broadway\Domain\DomainMessage;
use Broadway\Saga\State\Criteria;
use PHPUnit\Framework\TestCase;

class StaticallyConfiguredSagaMetadataTest extends TestCase
{
    /**
     * @var Metadata
     */
    private $metadata;

    public function setUp(): void
    {
        $this->metadata = new Metadata([
            'StaticallyConfiguredSagaMetadataTestSagaTestEvent1' => function () {
                return new Criteria(['criteria']);
            },
        ]);
    }

    /**
     * @test
     */
    public function it_handles_an_event_if_its_specified_by_the_saga()
    {
        $event = new StaticallyConfiguredSagaMetadataTestSagaTestEvent1();
        $domainMessage = $this->createDomainMessage($event);

        $this->assertTrue($this->metadata->handles($domainMessage));
    }

    /**
     * @test
     */
    public function it_does_not_handle_an_event_if_its_not_specified_by_the_saga()
    {
        $event = new StaticallyConfiguredSagaMetadataTestSagaTestEvent2();
        $domainMessage = $this->createDomainMessage($event);

        $this->assertFalse($this->metadata->handles($domainMessage));
    }

    /**
     * @test
     */
    public function it_returns_the_criteria_for_a_configured_event()
    {
        $event = new StaticallyConfiguredSagaMetadataTestSagaTestEvent1();
        $domainMessage = $this->createDomainMessage($event);

        $this->assertEquals(new Criteria(['criteria']), $this->metadata->criteria($domainMessage));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_there_is_no_criteria_for_a_given_event()
    {
        $this->expectException('RuntimeException');
        $event = new StaticallyConfiguredSagaMetadataTestSagaTestEvent2();
        $domainMessage = $this->createDomainMessage($event);

        $this->metadata->criteria($domainMessage);
    }

    private function createDomainMessage($event): DomainMessage
    {
        return DomainMessage::recordNow('id', 0, new \Broadway\Domain\Metadata([]), $event);
    }
}

class StaticallyConfiguredSagaMetadataTestSagaTestEvent1
{
}
class StaticallyConfiguredSagaMetadataTestSagaTestEvent2
{
}
