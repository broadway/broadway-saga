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

interface MetadataFactoryInterface
{
    /**
     * Creates and returns the Metadata for the given saga class.
     *
     * @param string $saga
     *
     * @return \Broadway\Saga\MetadataInterface
     */
    public function create($saga);
}
