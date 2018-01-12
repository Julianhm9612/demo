<?php

/**
 * PHP Service Bus (CQS implementation)
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBusDemo\Customer\Services;

use Desperado\EventSourcing\Service\EventSourcingService;
use Desperado\ServiceBus\Annotations;
use Desperado\ServiceBusDemo\Application\ApplicationContext;
use Desperado\ServiceBusDemo\Customer\Command as CustomerCommands;
use Desperado\ServiceBusDemo\Customer\CustomerAggregate;
use Desperado\ServiceBusDemo\Customer\Event as CustomerEvents;
use Desperado\ServiceBusDemo\Customer\Identity\CustomerAggregateIdentifier;
use Desperado\ServiceBus\Services\ServiceInterface;
use React\Promise\PromiseInterface;

/**
 * @Annotations\Service(
 *     loggerChannel="customerVerification"
 * )
 */
class CustomerVerificationService implements ServiceInterface
{
    /**
     * @Annotations\CommandHandler()
     *
     * @param CustomerCommands\SendCustomerVerificationMessageCommand $command
     * @param ApplicationContext                                      $context
     * @param EventSourcingService                                    $eventSourcingService
     *
     * @return PromiseInterface
     */
    public function executeSendCustomerVerificationMessageCommand(
        CustomerCommands\SendCustomerVerificationMessageCommand $command,
        ApplicationContext $context,
        EventSourcingService $eventSourcingService
    ): PromiseInterface
    {
        return $eventSourcingService
            ->obtainAggregate(new CustomerAggregateIdentifier($command->getCustomerIdentifier()))
            ->then(
                function(CustomerAggregate $aggregate = null) use ($command, $context)
                {
                    if(null !== $aggregate)
                    {
                        /**
                         * Generating a token and sending it, for example, to the user's email
                         *
                         * Suppose that somewhere we have done the sending and are waiting for confirmation
                         *
                         * For the test, we simulate the confirmation (correct) ourselves
                         */

                        $context->delivery(
                            CustomerEvents\CustomerVerificationTokenReceivedEvent::create([
                                'requestId'  => $command->getRequestId(),
                                'identifier' => $aggregate->getId()->toString()
                            ])
                        );

                        return;
                    }

                    /** Notification for a non-existent user requested */

                    $context->delivery(
                        CustomerEvents\CustomerAggregateNotFoundEvent::create([
                            'requestId'  => $command->getRequestId(),
                            'identifier' => $command->getCustomerIdentifier()
                        ])
                    );

                    return;
                }
            );
    }
}
