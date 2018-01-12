<?php

/**
 * PHP Service Bus (CQS implementation)
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBusDemo\Application;

use Desperado\EventSourcing\Service\EventSourcingService;
use Desperado\ServiceBus\Application\AbstractKernel;
use Desperado\ServiceBusDemo\Customer;
use Desperado\ServiceBusDemo\EmailNotifications;

/**
 * Application kernel
 */
class Kernel extends AbstractKernel
{
    /**
     * Process event sourcing configuration
     *
     * @param EventSourcingService $eventSourcingService
     *
     * @return void
     */
    public function configureEventSourcing(EventSourcingService $eventSourcingService): void
    {
        foreach($this->getAggregates() as $aggregateNamespace => $identifierNamespace)
        {
            $eventSourcingService->configure($aggregateNamespace, $identifierNamespace);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSagasList(): array
    {
        return [
            Customer\CustomerVerificationSaga::class
        ];
    }

    /**
     * Get aggregates list
     *
     * [
     *     'someAggregateNamespace' => 'someAggregateIdentityClassNamespace',
     *     'someAggregateNamespace' => 'someAggregateIdentityClassNamespace',
     *     ....
     * ]
     *
     *
     * @return array
     */
    protected function getAggregates(): array
    {
        return [
            Customer\CustomerAggregate::class  => Customer\Identity\CustomerAggregateIdentifier::class,
            Customer\CustomerEmailIndex::class => Customer\Identity\CustomerEmailIndexIdentifier::class
        ];
    }
}
