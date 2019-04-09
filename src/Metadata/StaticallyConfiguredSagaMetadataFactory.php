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
use RuntimeException;

/**
 * Class StaticallyConfiguredSagaMetadataFactory
 * @package Broadway\Saga\Metadata
 */
class StaticallyConfiguredSagaMetadataFactory implements MetadataFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($saga): MetadataInterface
    {
        $requiredInterface = StaticallyConfiguredSagaInterface::class;

        if (! is_subclass_of($saga, $requiredInterface)) {
            throw new RuntimeException(
                sprintf('Provided saga of class %s must implement %s', get_class($saga), $requiredInterface)
            );
        }

        /** @var StaticallyConfiguredSagaInterface $saga */
        $criteria = $saga::configuration();

        return new Metadata($criteria);
    }
}
