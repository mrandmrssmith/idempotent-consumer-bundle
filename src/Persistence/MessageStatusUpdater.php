<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Persistence;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;

interface MessageStatusUpdater
{
    public function update(MessageStatus $messageStatus): void;
}
