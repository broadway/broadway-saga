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

use Broadway\Domain\DomainMessage;
use Broadway\EventDispatcher\EventDispatcher;
use Broadway\Saga\Metadata\CatchableSagaInterface;
use Broadway\Saga\Metadata\MetadataFactoryInterface;
use Broadway\Saga\State\RepositoryInterface;
use Broadway\Saga\State\StateManagerInterface;
use Throwable;

/**
 * SagaManager that manages multiple sagas.
 */
class MultipleSagaManager implements SagaManagerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var string[]
     */
    private $sagas;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * MultipleSagaManager constructor.
     *
     * @param RepositoryInterface $repository
     * @param array $sagas
     * @param StateManagerInterface $stateManager
     * @param MetadataFactoryInterface $metadataFactory
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        RepositoryInterface $repository,
        array $sagas,
        StateManagerInterface $stateManager,
        MetadataFactoryInterface $metadataFactory,
        EventDispatcher $eventDispatcher
    ) {
        $this->repository      = $repository;
        $this->sagas           = $sagas;
        $this->stateManager    = $stateManager;
        $this->metadataFactory = $metadataFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Handles the event by delegating it to Saga('s) related to the event.
     *
     * @param DomainMessage $domainMessage
     *
     * @throws Throwable
     */
    public function handle(DomainMessage $domainMessage): void
    {

        /** @var Saga $saga */
        foreach ($this->sagas as $sagaType => $saga) {
            $metadata = $this->metadataFactory->create($saga);

            if (! $metadata->handles($domainMessage)) {
                continue;
            }

            $state = $this->stateManager->findOneBy($metadata->criteria($domainMessage), $sagaType);

            if (null === $state) {
                continue;
            }
            $this->eventDispatcher->dispatch(
                SagaManagerInterface::EVENT_PRE_HANDLE,
                [$state, $domainMessage]
            );

            $newState = $saga->handle($state, $domainMessage);

            $this->eventDispatcher->dispatch(
                SagaManagerInterface::EVENT_POST_HANDLE,
                [$state, $domainMessage]
            );

            $this->repository->save($newState);

            if ($saga instanceof CatchableSagaInterface && $saga->isThrowException() && !$saga->isExceptionCaught()) {
                throw $saga->getException();
            }
        }
    }

    /**
     * Find and return saga object by saga type name
     *
     * @param string $sagaType
     *
     * @return SagaInterface|null
     */
    public function getSagaByType(string $sagaType): ?SagaInterface
    {
        return $this->sagas[$sagaType] ?? null;
    }
}
