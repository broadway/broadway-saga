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

use Broadway\Domain\DomainMessage;
use Broadway\Saga\MetadataInterface;
use Broadway\Saga\State\Criteria;
use RuntimeException;

class Metadata implements MetadataInterface
{
    /**
     * @var array
     */
    private $criteria;

    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function handles(DomainMessage $domainMessage): bool
    {
        $eventName = $this->getClassName($domainMessage);

        return isset($this->criteria[$eventName]);
    }

    /**
     * {@inheritdoc}
     */
    public function criteria(DomainMessage $domainMessage): ?Criteria
    {
        $eventName = $this->getClassName($domainMessage);
        if (!$this->handles($domainMessage)) {
            throw new RuntimeException(sprintf("No criteria for event '%s'.", $eventName));
        }

        return $this->criteria[$eventName]($domainMessage->getPayload(), $domainMessage);
    }

    /**
     * @return string
     */
    private function getClassName(DomainMessage $domainMessage)
    {
        $event = $domainMessage->getPayload();
        $classParts = explode('\\', get_class($event));

        return end($classParts);
    }
}
