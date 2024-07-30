<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Resolver;

use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;

interface IdempotentKeyResolver
{
    public function resolveKey(IncomingMessage $message): string;

    public function supports(IncomingMessage $message): bool;
}
