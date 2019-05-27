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
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;

/**
 * Class SagaMetadataEnricher
 * @package Broadway\Saga
 */
class SagaMetadataEnricher implements MetadataEnricher
{
    /**
     * @var array
     */
    private $sagaData = [];

    /**
     * Saga post handle event action
     *
     * @param State $state
     * @param DomainMessage $domainMessage
     */
    public function postHandleSaga(State $state, DomainMessage $domainMessage): void
    {
        $this->sagaData = ['type' => $state->getSagaId(), 'state_id' => $state->getId()];
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

        $newMetadata = new Metadata(['saga' => $this->sagaData]);
        $metadata    = $metadata->merge($newMetadata);

        return $metadata;
    }
}
