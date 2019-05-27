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

use Broadway\Saga\SagaInterface;

/**
 * Class CatchableSagaInterface
 *
 * @category Metadata
 * @package Broadway\Saga
 */
interface CatchableSagaInterface extends SagaInterface
{
    /**
     * Is was exception while handle event
     *
     * @return bool
     */
    public function isThrowException(): bool;

    /**
     * Checks whether exception is caught
     *
     * @return boolean
     */
    public function isExceptionCaught(): bool;
}
