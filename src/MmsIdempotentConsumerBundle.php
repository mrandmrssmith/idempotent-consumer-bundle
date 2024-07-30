<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle;

use MrAndMrsSmith\IdempotentConsumerBundle\DependencyInjection\AddKeyResolverTagToInterfaceImplementationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MmsIdempotentConsumerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
