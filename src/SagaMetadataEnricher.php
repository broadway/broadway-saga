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
     * @param $type
     * @param $id
     */
    public function postHandleSaga($type, $id): void
    {
        $this->sagaData = ['type' => $type, 'state_id' => $id];
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
