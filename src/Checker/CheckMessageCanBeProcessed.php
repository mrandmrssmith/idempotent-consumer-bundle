<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Checker;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusPersister;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusRetriever;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusUpdater;
use MrAndMrsSmith\IdempotentConsumerBundle\ProcessFailedMessageVoter\ProcessFailedMessageVoter;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;

class CheckMessageCanBeProcessed
{
    /** @var KeyResolverRegister */
    private $idempotentKeyResolversRegister;

    /** @var MessageStatusRetriever */
    private $messageStatusRetriever;

    /** @var MessageStatusPersister */
    private $messageStatusPersister;

    /** @var MessageStatusUpdater */
    private $messageStatusUpdater;

    /** @var ProcessFailedMessageVoter */
    private $processFailedMessageVoter;

    public function __construct(
        KeyResolverRegister $idempotentKeyResolversRegister,
        MessageStatusRetriever $messageStatusRetriever,
        MessageStatusPersister $messageStatusPersister,
        MessageStatusUpdater $messageStatusUpdater,
        ProcessFailedMessageVoter $processFailedMessageVoter
    ) {
        $this->messageStatusRetriever = $messageStatusRetriever;
        $this->idempotentKeyResolversRegister = $idempotentKeyResolversRegister;
        $this->messageStatusPersister = $messageStatusPersister;
        $this->messageStatusUpdater = $messageStatusUpdater;
        $this->processFailedMessageVoter = $processFailedMessageVoter;
    }

    public function check(IncomingMessage $message): bool
    {
        $key = $this->idempotentKeyResolversRegister->getResolver($message)->resolveKey($message);
        $messageStatus = $this->messageStatusRetriever->retrieve($key);
        if (!$messageStatus instanceof MessageStatus) {
            $messageStatus = MessageStatus::createNew($key, $message->getName());
            $this->messageStatusPersister->persist($messageStatus);
        }
        $statusAllowProcessing = $messageStatus->statusAllowProcessing();
        if ($messageStatus->isFailed()) {
            $statusAllowProcessing = $this->processFailedMessageVoter->vote($message);
        }
        if ($statusAllowProcessing === false) {
            return false;
        }
        $messageStatus->start();
        $this->messageStatusUpdater->update($messageStatus);

        return true;
    }
}
