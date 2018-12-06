<?php

/**
 * Demo application, remotely similar to Uber
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\Customer\Events;

use Desperado\ServiceBus\Common\Contract\Messages\Event;

/**
 * Customer aggregate created
 *
 * internal event
 */
final class CustomerAggregateCreated implements Event
{
    /**
     * Customer aggregate id
     *
     * @var string
     */
    public $id;

    /**
     * Phone number
     *
     * @var string
     */
    public $phone;

    /**
     * Email address
     *
     * @var string
     */
    public $email;

    /**
     * First name
     *
     * @var string
     */
    public $firstName;

    /**
     * Last name
     *
     * @var string
     */
    public $lastName;

    /**
     * @param string $id
     * @param string $phone
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     *
     * @return self
     */
    public static function create(string $id, string $phone, string $email, string $firstName, string $lastName): self
    {
        $self = new self();

        $self->id        = $id;
        $self->phone     = $phone;
        $self->email     = $email;
        $self->firstName = $firstName;
        $self->lastName  = $lastName;

        return $self;
    }

    private function __construct()
    {

    }
}
