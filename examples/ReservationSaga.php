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

namespace Broadway\Saga\Examples;

require __DIR__.'/../vendor/autoload.php';

use Broadway\CommandHandling\CommandBus;
use Broadway\Saga\Metadata\StaticallyConfiguredSagaInterface;
use Broadway\Saga\Saga;
use Broadway\Saga\State;
use Broadway\Saga\State\Criteria;
use Broadway\UuidGenerator\UuidGeneratorInterface;

class ReservationSaga extends Saga implements StaticallyConfiguredSagaInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    public function __construct(
        CommandBus $commandBus,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
    }

    public static function configuration(): array
    {
        return [
            'OrderPlaced' => function (OrderPlaced $event) {
                return null; // no criteria, start of a new saga
            },
            'ReservationAccepted' => function (ReservationAccepted $event) {
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId(),
                ]);
            },
            'ReservationRejected' => function (ReservationRejected $event) {
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId(),
                ]);
            },
        ];
    }

    public function handleOrderPlaced(OrderPlaced $event, State $state): State
    {
        // keep the order id, for reference in `handleReservationAccepted()` and `handleReservationRejected()`
        $state->set('orderId', $event->orderId());

        // generate an id for the reservation
        $reservationId = $this->uuidGenerator->generate();
        $state->set('reservationId', $reservationId);

        // make the reservation
        $command = new MakeSeatReservation($reservationId, $event->numberOfSeats());
        $this->commandBus->dispatch($command);

        return $state;
    }

    public function handleReservationAccepted(ReservationAccepted $event, State $state): State
    {
        // the seat reservation for the given order is has been accepted, mark the order as booked
        $command = new MarkOrderAsBooked($state->get('orderId'));
        $this->commandBus->dispatch($command);

        // the saga ends here
        $state->setDone();

        return $state;
    }

    public function handleReservationRejected(ReservationRejected $event, State $state): State
    {
        // the seat reservation for the given order is has been rejected, reject the order as well
        $command = new RejectOrder($state->get('orderId'));
        $this->commandBus->dispatch($command);

        // the saga ends here
        $state->setDone();

        return $state;
    }
}

/**
 * event.
 */
class OrderPlaced
{
    /**
     * @var string
     */
    private $orderId;

    /**
     * @var int
     */
    private $numberOfSeats;

    public function __construct(string $orderId, int $numberOfSeats)
    {
        $this->orderId = $orderId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function numberOfSeats(): int
    {
        return $this->numberOfSeats;
    }
}

/**
 * command.
 */
class MakeSeatReservation
{
    /**
     * @var string
     */
    private $reservationId;

    /**
     * @var int
     */
    private $numberOfSeats;

    public function __construct(string $reservationId, int $numberOfSeats)
    {
        $this->reservationId = $reservationId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function reservationId(): string
    {
        return $this->reservationId;
    }

    public function numberOfSeats(): int
    {
        return $this->numberOfSeats;
    }
}

/**
 * event.
 */
class ReservationAccepted
{
    /**
     * @var string
     */
    private $reservationId;

    public function __construct(string $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function reservationId(): string
    {
        return $this->reservationId;
    }
}

/**
 * event.
 */
class ReservationRejected
{
    /**
     * @var string
     */
    private $reservationId;

    public function __construct(string $reservationId)
    {
        $this->reservationId = $reservationId;
    }

    public function reservationId(): string
    {
        return $this->reservationId;
    }
}

/**
 * command.
 */
class MarkOrderAsBooked
{
    /**
     * @var string
     */
    private $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }
}

/**
 * command.
 */
class RejectOrder
{
    /**
     * @var string
     */
    private $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }
}
