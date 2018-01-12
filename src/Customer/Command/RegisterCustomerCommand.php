<?php

/**
 * PHP Service Bus (CQS implementation)
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBusDemo\Customer\Command;

use Symfony\Component\Validator\Constraints as Assert;
use Desperado\Domain\Message\AbstractCommand;

/**
 * Start customer registration
 *
 * @see CustomerRegistrationCompleteEvent
 * @see FailedRegistrationEvent
 * @see CustomerAlreadyExistsEvent
 */
class RegisterCustomerCommand extends AbstractCommand
{
    /**
     * Operation identifier
     *
     * @Assert\NotBlank(
     *     message="operation identifier must be specified"
     * )
     *
     * @var string
     */
    protected $requestId;

    /**
     * User name
     *
     * @Assert\NotBlank(
     *     message="customer user name must be specified"
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 30,
     *      minMessage = "customer user name must be at least {{ limit }} characters long",
     *      maxMessage = "customer user name cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    protected $userName;

    /**
     * Display name
     *
     * @Assert\NotBlank(
     *     message="customer display name must be specified"
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 30,
     *      minMessage = "customer display name must be at least {{ limit }} characters long",
     *      maxMessage = "customer display name cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    protected $displayName;

    /**
     * Email
     *
     * @Assert\NotBlank(
     *     message="customer email hash must be specified"
     * )
     * @Assert\Email(
     *     message="email user is incorrect"
     * )
     *
     * @var string
     */
    protected $email;

    /**
     * Password
     *
     * @Assert\NotBlank(
     *     message="customer password must be specified"
     * )
     * @Assert\Length(
     *      min = 5,
     *      max = 15,
     *      minMessage = "customer password must be at least {{ limit }} characters long",
     *      maxMessage = "customer password cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    protected $password;

    /**
     * Get operation identifier
     *
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Get display name
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
