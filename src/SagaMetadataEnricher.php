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

namespace Broadway\Saga;

use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;

class SagaMetadataEnricher implements MetadataEnricher
{
    /**
     * @var array
     */
    private $sagaData = [];

    public function postHandleSaga(string $type, string $id): void
    {
        $this->sagaData = ['type' => $type, 'state_id' => $id];
    }

    public function enrich(Metadata $metadata): Metadata
    {
        if (0 === count($this->sagaData)) {
            return $metadata;
        }

        $newMetadata = new Metadata(['saga' => $this->sagaData]);
        $metadata = $metadata->merge($newMetadata);

        return $metadata;
    }
}
