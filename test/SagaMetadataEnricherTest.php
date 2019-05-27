<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Broadway\Saga;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use PHPUnit\Framework\TestCase;

/**
 * Class SagaMetadataEnricherTest
 * @package Broadway\Saga
 */
class SagaMetadataEnricherTest extends TestCase
{
    /**
     * @var SagaMetadataEnricher
     */
    private $sagaMetadataEnricher;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     *
     */
    public function setUp()
    {
        $this->sagaMetadataEnricher = new SagaMetadataEnricher();
        $this->metadata             = new Metadata(['yolo' => 'tralelo']);
    }

    /**
     * @test
     */
    public function it_stores_the_state(): void
    {
        $type = 'type';
        $id   = 'id';
        $state = new State($id, $type);
        $domainMessage = DomainMessage::recordNow($id, 0, new Metadata([]), []);
        $this->sagaMetadataEnricher->postHandleSaga($state, $domainMessage);

        $actual = $this->sagaMetadataEnricher->enrich($this->metadata);

        $expected = $this->metadata->merge(Metadata::kv('saga', ['type' => $type, 'state_id' => $id]));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_uses_the_latest_saga_data_it_received(): void
    {
        $type1 = 'type1';
        $id1   = 'id1';
        $state1 = new State($id1, $type1);
        $domainMessage1 = DomainMessage::recordNow($id1, 0, new Metadata([]), []);
        $this->sagaMetadataEnricher->postHandleSaga($state1, $domainMessage1);


        $type2 = 'type2';
        $id2   = 'id2';
        $state2 = new State($id2, $type2);
        $domainMessage2 = DomainMessage::recordNow($id2, 0, new Metadata([]), []);
        $this->sagaMetadataEnricher->postHandleSaga($state2, $domainMessage2);

        $actual = $this->sagaMetadataEnricher->enrich($this->metadata);

        $expected = $this->metadata->merge(Metadata::kv('saga', ['type' => 'type2', 'state_id' => 'id2']));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_enriches_multiple_instances_of_metadata(): void
    {
        $type = 'type';
        $id   = 'id';
        $state = new State($id, $type);
        $domainMessage = DomainMessage::recordNow($id, 0, new Metadata([]), []);
        $this->sagaMetadataEnricher->postHandleSaga($state, $domainMessage);

        $this->sagaMetadataEnricher->enrich($this->metadata);
        $actual = $this->sagaMetadataEnricher->enrich($this->metadata);

        $expected = $this->metadata->merge(Metadata::kv('saga', ['type' => 'type', 'state_id' => 'id']));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param Metadata $metadata
     *
     * @return Metadata
     */
    public function enrich(Metadata $metadata): Metadata
    {
        if (count($this->sagaData) === 0) {
            return $metadata;
        }

        $newMetadata = new Metadata([['saga' => $this->sagaData]]);
        $metadata    = $metadata->merge($newMetadata);

        return $metadata;
    }
}
