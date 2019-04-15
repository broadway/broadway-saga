<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/../vendor/autoload.php';

use Broadway\CommandHandling\CommandBus;
use Broadway\Saga\Metadata\StaticallyConfiguredSagaInterface;
use Broadway\Saga\Saga;
use Broadway\Saga\State;
use Broadway\Saga\State\Criteria;
use Broadway\UuidGenerator\UuidGeneratorInterface;

/**
 * Class ReservationSaga
 */
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

    /**
     * ReservationSaga constructor.
     *
     * @param CommandBus $commandBus
     * @param UuidGeneratorInterface $uuidGenerator
     */
    public function __construct(
        CommandBus $commandBus,
        UuidGeneratorInterface $uuidGenerator
    ){
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @return array
     */
    public static function configuration(): array
    {
        return [
            'OrderPlaced'         => static function (OrderPlaced $event){
                return null; // no criteria, start of a new saga
            },
            'ReservationAccepted' => static function (ReservationAccepted $event){
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId(),
                ]);
            },
            'ReservationRejected' => static function (ReservationRejected $event){
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId(),
                ]);
            },
            'BadMethodCall'=> static function (BadMethodCall $event){
                return null; // no criteria, start of a new saga
            },

        ];
    }



    /**
     * @param State $state
     * @param OrderPlaced $event
     *
     * @return State
     */
    public function handleOrderPlaced(State $state, OrderPlaced $event): State
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

    /**
     * @param State $state
     * @param ReservationAccepted $event
     *
     * @return State
     */
    public function handleReservationAccepted(State $state, ReservationAccepted $event): State
    {
        // the seat reservation for the given order is has been accepted, mark the order as booked
        $command = new MarkOrderAsBooked($state->get('orderId'));
        $this->commandBus->dispatch($command);

        // the saga ends here
        $state->setDone();

        return $state;
    }

    /**
     * @param State $state
     * @param ReservationRejected $event
     *
     * @return State
     */
    public function handleReservationRejected(State $state, ReservationRejected $event): State
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
 * event
 */
class OrderPlaced
{
    /**
     * @var
     */
    private $orderId;
    /**
     * @var
     */
    private $numberOfSeats;

    /**
     * OrderPlaced constructor.
     *
     * @param $orderId
     * @param $numberOfSeats
     */
    public function __construct($orderId, $numberOfSeats)
    {
        $this->orderId = $orderId;
        $this->numberOfSeats = $numberOfSeats;
    }

    /**
     * @return mixed
     */
    public function orderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function numberOfSeats()
    {
        return $this->numberOfSeats;
    }
}

/**
 * command
 */
class MakeSeatReservation
{
    /**
     * @var
     */
    private $reservationId;
    /**
     * @var
     */
    private $numberOfSeats;

    /**
     * MakeSeatReservation constructor.
     *
     * @param $reservationId
     * @param $numberOfSeats
     */
    public function __construct($reservationId, $numberOfSeats)
    {
        $this->reservationId = $reservationId;
        $this->numberOfSeats = $numberOfSeats;
    }

    /**
     * @return mixed
     */
    public function reservationId()
    {
        return $this->reservationId;
    }

    /**
     * @return mixed
     */
    public function numberOfSeats()
    {
        return $this->numberOfSeats;
    }
}

/**
 * event
 */
class ReservationAccepted
{
    /**
     * @var
     */
    private $reservationId;

    /**
     * ReservationAccepted constructor.
     *
     * @param $reservationId
     */
    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * @return mixed
     */
    public function reservationId()
    {
        return $this->reservationId;
    }
}

/**
 * event
 */
class ReservationRejected
{
    /**
     * @var
     */
    private $reservationId;

    /**
     * ReservationRejected constructor.
     *
     * @param $reservationId
     */
    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

    /**
     * @return mixed
     */
    public function reservationId()
    {
        return $this->reservationId;
    }
}

/**
 * command
 */
class MarkOrderAsBooked
{
    /**
     * @var
     */
    private $orderId;

    /**
     * MarkOrderAsBooked constructor.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }
}

/**
 * command
 */
class RejectOrder
{
    /**
     * @var
     */
    private $orderId;

    /**
     * RejectOrder constructor.
     *
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }
}

/**
 * Class BadMethodCall
 */
class BadMethodCall
{
    /**
     * @var
     */
    private $foo;

    /**
     * @param $orderId
     */
    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }
}