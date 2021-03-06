<?php

/**
 * PHP Service Bus demo application
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */
declare(strict_types = 1);

namespace App\DriverDocument\Data;

use function ServiceBus\Common\uuid;

/**
 * Uploaded document id
 */
final class DriverDocumentId
{
    /**
     * @var string
     */
    private $value;

    /**
     * @return self
     */
    public static function new(): self
    {
        return new self(uuid());
    }

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
