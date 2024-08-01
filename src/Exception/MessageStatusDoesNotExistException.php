<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Exception;

class MessageStatusDoesNotExistException extends \Exception
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('MessageStatus for key %s does not exist', $key));
    }
}
