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

use RuntimeException;

class StaticallyConfiguredSagaMetadataFactory implements MetadataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($saga)
    {
        $requiredInterface = StaticallyConfiguredSagaInterface::class;

        if (!is_subclass_of($saga, $requiredInterface)) {
            throw new RuntimeException(sprintf('Provided saga of class %s must implement %s', get_class($saga), $requiredInterface));
        }

        $criteria = $saga::configuration();

        return new Metadata($criteria);
    }
}
