<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Finalizer;

use MrAndMrsSmith\IdempotentConsumerBundle\Exception\KeyResolverNotFoundException;
use MrAndMrsSmith\IdempotentConsumerBundle\Exception\MessageStatusDoesNotExistException;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusRetriever;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusUpdater;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;

class MessageFinalizer
{
    /** @var MessageStatusUpdater */
    private $messageUpdater;

    /** @var KeyResolverRegister */
    private $keyResolverRegister;

    /** @var MessageStatusRetriever */
    private $messageStatusRetriever;

    public function __construct(
        MessageStatusUpdater $messageStatusUpdater,
        KeyResolverRegister $keyResolverRegister,
        MessageStatusRetriever $messageStatusRetriever
    ) {
        $this->messageUpdater = $messageStatusUpdater;
        $this->keyResolverRegister = $keyResolverRegister;
        $this->messageStatusRetriever = $messageStatusRetriever;
    }

    /** @throws MessageStatusDoesNotExistException|KeyResolverNotFoundException */
    public function finalizeSuccess(IncomingMessage $incomingMessage): void
    {
        $messageStatus = $this->getMessageStatusFromIncomingMessage($incomingMessage);
        $messageStatus->finish();

        $this->messageUpdater->update($messageStatus);
    }

    /** @throws MessageStatusDoesNotExistException|KeyResolverNotFoundException */
    public function finalizeFailure(IncomingMessage $incomingMessage): void
    {
        $messageStatus = $this->getMessageStatusFromIncomingMessage($incomingMessage);
        $messageStatus->fail();

        $this->messageUpdater->update($messageStatus);
    }

    public function markAsRetry(IncomingMessage $incomingMessage): void
    {
        $messageStatus = $this->getMessageStatusFromIncomingMessage($incomingMessage);
        $messageStatus->markAsRetry();

        $this->messageUpdater->update($messageStatus);
    }

    /** @throws KeyResolverNotFoundException|MessageStatusDoesNotExistException */
    private function getMessageStatusFromIncomingMessage(IncomingMessage $message): MessageStatus
    {
        $key = $this->keyResolverRegister->getResolver($message)->resolveKey($message);

        $messageStatus = $this->messageStatusRetriever->retrieve($key);
        if (!$messageStatus instanceof MessageStatus) {
            throw new MessageStatusDoesNotExistException($key);
        }

        return $messageStatus;
    }
}
