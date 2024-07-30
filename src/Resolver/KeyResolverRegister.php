<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\Resolver;

use MrAndMrsSmith\IdempotentConsumerBundle\Exception\KeyResolverNotFoundException;
use MrAndMrsSmith\IdempotentConsumerBundle\Message\IncomingMessage;

class KeyResolverRegister
{
    /**
     * @var IdempotentKeyResolver[]
     */
    private $keyResolvers;

    public function __construct(iterable $keyResolvers)
    {
        $this->keyResolvers = $keyResolvers;
    }

    /**
     * @throws KeyResolverNotFoundException
     */
    public function getResolver(IncomingMessage $message): IdempotentKeyResolver
    {
        foreach ($this->keyResolvers as $resolver) {
            if ($resolver->supports($message)) {
                return $resolver;
            }
        }

        throw new KeyResolverNotFoundException($message);
    }
}
