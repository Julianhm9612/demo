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
use Desperado\Saga\Service\SagaService;
use Desperado\ServiceBus\Annotations;
use Desperado\ServiceBusDemo\Application\ApplicationContext;
use Desperado\ServiceBusDemo\Customer\Command as CustomerCommands;
use Desperado\ServiceBusDemo\Customer\CustomerAggregate;
use Desperado\ServiceBusDemo\Customer\CustomerEmailIndex;
use Desperado\ServiceBusDemo\Customer\Event as CustomerEvents;
use Desperado\ServiceBusDemo\Customer\Identity as CustomerIdentities;
use Desperado\ServiceBus\Services\Handlers\Exceptions\UnfulfilledPromiseData;
use Desperado\ServiceBus\Services\ServiceInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;

/**
 * @Annotations\Service()
 */
class RegisterCustomerService implements ServiceInterface
{
    /**
     * @Annotations\CommandHandler
     *
     * @param CustomerCommands\RegisterCustomerCommand $command
     * @param ApplicationContext                       $context
     * @param EventSourcingService                     $eventSourcingService
     *
     * @return PromiseInterface
     */
    public function executeRegisterCustomerCommand(
        CustomerCommands\RegisterCustomerCommand $command,
        ApplicationContext $context,
        EventSourcingService $eventSourcingService
    ): PromiseInterface
    {
        return $eventSourcingService
            ->obtainIndex(CustomerEmailIndex::class)
            ->then(
                function(CustomerEmailIndex $customerEmailIndex) use ($command, $context, $eventSourcingService)
                {
                    /** new customer */
                    if(false === $customerEmailIndex->hasIdentifier($command->getEmail()))
                    {
                        /** @var CustomerIdentities\CustomerAggregateIdentifier $customerIdentifier */
                        $customerIdentifier = CustomerIdentities\CustomerAggregateIdentifier::newUuid();

                        $eventSourcingService
                            ->createAggregate($customerIdentifier)
                            ->then(
                                function(CustomerAggregate $aggregate) use (
                                    $customerEmailIndex,
                                    $customerIdentifier,
                                    $command
                                )
                                {
                                    $aggregate->registerCustomer($command);

                                    $customerEmailIndex->store($command->getEmail(), $customerIdentifier);
                                }
                            );
                    }
                    else
                    {
                        $context->delivery(
                            CustomerEvents\CustomerAlreadyExistsEvent::create([
                                'requestId'  => $command->getRequestId(),
                                'identifier' => $customerEmailIndex->getIdentifier($command->getEmail())->toString()
                            ])
                        );
                    }
                }
            );
    }

    /**
     * @Annotations\ErrorHandler(
     *     message="Desperado\ServiceBusDemo\Customer\Command\RegisterCustomerCommand",
     *     type="Exception",
     *     loggerChannel="registrationFail"
     * )
     *
     * @param UnfulfilledPromiseData $unfulfilledPromiseData
     *
     * @return PromiseInterface
     */
    public function failedRegisterCustomerCommand(UnfulfilledPromiseData $unfulfilledPromiseData): PromiseInterface
    {
        return new Promise(
            function() use ($unfulfilledPromiseData)
            {
                /** @var CustomerCommands\RegisterCustomerCommand $registerCommand */
                $registerCommand = $unfulfilledPromiseData->getMessage();

                $unfulfilledPromiseData
                    ->getContext()
                    ->delivery(
                        CustomerEvents\FailedRegistrationEvent::create([
                            'requestId' => $registerCommand->getRequestId(),
                            'reason'    => $unfulfilledPromiseData->getThrowable()->getMessage()
                        ])
                    );
            }
        );
    }

    /**
     * @Annotations\EventHandler()
     *
     * @param CustomerEvents\CustomerRegisteredEvent $event
     * @param ApplicationContext                     $context
     * @param SagaService                            $sagaService
     *
     * @return PromiseInterface
     *
     * @throws \Throwable
     */
    public function whenCustomerRegisteredEvent(
        CustomerEvents\CustomerRegisteredEvent $event,
        ApplicationContext $context,
        SagaService $sagaService
    ): PromiseInterface
    {
        return $sagaService->startSaga(
            new CustomerIdentities\CustomerVerificationSagaIdentifier($event->getRequestId()),
            CustomerCommands\StartVerificationSagaCommand::create(['customerIdentifier' => $event->getIdentifier()])
        );
    }
}
