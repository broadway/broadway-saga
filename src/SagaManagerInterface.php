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

use Broadway\EventHandling\EventListener;

interface SagaManagerInterface extends EventListener
{
    public const EVENT_PRE_HANDLE = 'broadway.saga.pre_handle';
    public const EVENT_POST_HANDLE = 'broadway.saga.post_handle';
}
