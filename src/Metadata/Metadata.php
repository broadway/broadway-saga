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

use Broadway\Domain\DomainMessage;
use Broadway\Saga\MetadataInterface;
use RuntimeException;

class Metadata implements MetadataInterface
{
    private $criteria;

    /**
     * @param array $criteria
     */
    public function __construct($criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritDoc}
     */
    public function handles(DomainMessage $domainMessage)
    {
        $eventName = $this->getClassName($domainMessage);

        return isset($this->criteria[$eventName]);
    }

    /**
     * {@inheritDoc}
     */
    public function criteria(DomainMessage $domainMessage)
    {
        $eventName = $this->getClassName($domainMessage);
        if (!$this->handles($domainMessage)) {
            throw new RuntimeException(sprintf("No criteria for event '%s'.", $eventName));
        }

        return $this->criteria[$eventName]($domainMessage->getPayload(), $domainMessage);
    }

    private function getClassName(DomainMessage $domainMessage)
    {
        $event = $domainMessage->getPayload();
        $classParts = explode('\\', get_class($event));

        return end($classParts);
    }
}
