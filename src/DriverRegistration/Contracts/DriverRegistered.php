<?php

/**
 * Demo application, remotely similar to Uber
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\DriverRegistration\Contracts;

use Desperado\ServiceBus\Common\Contract\Messages\Event;

/**
 * New driver successful registered
 *
 * @api
 * @see RegisterDriver
 */
final class DriverRegistered implements Event
{
    /**
     * Request operation id
     *
     * @var string
     */
    public $correlationId;

    /**
     * Driver identifier
     *
     * @var string
     */
    public $driverId;

    /**
     * @param string $driverId
     * @param string $correlationId
     *
     * @return self
     */
    public static function create(string $driverId, string $correlationId): self
    {
        $self = new self();

        $self->driverId      = $driverId;
        $self->correlationId = $correlationId;

        return $self;
    }

    private function __construct()
    {

    }
}