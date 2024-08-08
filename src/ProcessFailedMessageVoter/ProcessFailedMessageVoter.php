<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\ProcessFailedMessageVoter;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;

interface ProcessFailedMessageVoter
{
    public function vote(IncomingMessage $message): bool;
}
