<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Persistence;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;

interface MessageStatusPersister
{
    public function persist(MessageStatus $messageStatus): void;
}
