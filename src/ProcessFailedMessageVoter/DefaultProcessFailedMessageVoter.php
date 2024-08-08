<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\ProcessFailedMessageVoter;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;

class DefaultProcessFailedMessageVoter implements ProcessFailedMessageVoter
{
    /** @var bool */
    private $wantToProcessFailedMessage;

    public function __construct(bool $wantToProcessFailedMessage)
    {
        $this->wantToProcessFailedMessage = $wantToProcessFailedMessage;
    }

    public function vote(IncomingMessage $message): bool
    {
        return $this->wantToProcessFailedMessage;
    }
}
