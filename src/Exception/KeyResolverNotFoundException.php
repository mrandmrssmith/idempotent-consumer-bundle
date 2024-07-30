<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Exception;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;

class KeyResolverNotFoundException extends \Exception
{
    public function __construct(IncomingMessage $message)
    {
        parent::__construct(sprintf('Key resolver not found for message %s', $message->getName()));
    }
}
