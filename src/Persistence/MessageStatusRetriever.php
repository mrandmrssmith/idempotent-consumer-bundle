<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Persistence;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\MessageStatus;

interface MessageStatusRetriever
{
    public function retrieve(string $key): ?MessageStatus;
}
