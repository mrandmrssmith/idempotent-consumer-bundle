<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Finalizer;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusRetriever;
use MrAndMrsSmith\IdempotentConsumerBundle\Persistence\MessageStatusUpdater;
use MrAndMrsSmith\IdempotentConsumerBundle\Resolver\KeyResolverRegister;

class MessageFinalizer
{
    private $messageUpdater;

    private $keyResolverRegister;

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

    public function finalizeSuccess(IncomingMessage $incomingMessage): void
    {
        $messageStatus = $this->getMessageStatusFromIncomingMessage($incomingMessage);
        $messageStatus->finish();

        $this->messageUpdater->update($messageStatus);
    }

    public function finalizeFailure(IncomingMessage $incomingMessage): void
    {
        $messageStatus = $this->getMessageStatusFromIncomingMessage($incomingMessage);
        $messageStatus->fail();

        $this->messageUpdater->update($messageStatus);
    }

    private function getMessageStatusFromIncomingMessage(IncomingMessage $message): MessageStatus
    {
        $key = $this->keyResolverRegister->getResolver($message)->resolveKey($message);

        return $this->messageStatusRetriever->retrieve($key);
    }
}
