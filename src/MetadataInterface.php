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

use Broadway\Saga\State\Criteria;

interface MetadataInterface
{
    /**
     * @param mixed $event
     *
     * @return bool True, if the saga can handle the event
     */
    public function handles($event);

    /**
     * @return Criteria Criteria for the given event
     */
    public function criteria($event);
}
