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

use Broadway\EventHandling\EventListener;

interface SagaManagerInterface extends EventListener
{
    const EVENT_PRE_HANDLE  = 'broadway.saga.pre_handle';
    const EVENT_POST_HANDLE = 'broadway.saga.post_handle';
}
