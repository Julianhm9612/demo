<?php

/**
 * PHP Service Bus demo application
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\DriverDocument\Exceptions;

/**
 *
 */
final class IncorrectDriverDocumentStatus extends \InvalidArgumentException
{
    /**
     * @param string $documentStatus
     */
    public function __construct(string $documentStatus)
    {
        parent::__construct(
            \sprintf('The specified document status ("%s") is incorrect', $documentStatus)
        );
    }
}
