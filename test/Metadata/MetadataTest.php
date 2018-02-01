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

class StaticallyConfiguredSagaMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Metadata
     */
    private $metadata;

    public function setUp()
    {
        $this->metadata = new Metadata([
            'StaticallyConfiguredSagaMetadataTestSagaTestEvent1' => function () {
                return 'criteria';
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

        $this->assertEquals('criteria', $this->metadata->criteria($domainMessage));
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function it_throws_an_exception_if_there_is_no_criteria_for_a_given_event()
    {
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
