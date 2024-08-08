<?php

namespace MrAndMrsSmith\IdempotentConsumerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Kernel;

final class MmsIdempotentConsumerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resource/'));
        $loader->load('services.yaml');
        if (Kernel::VERSION_ID < 40000) {
            $loader->load('services_resolver_register_3.yaml');
        } else {
            $loader->load('services_resolver_register.yaml');
        }

        $defaultFailedMessageProcessVoterDefinition = $container->getDefinition(
            'mms.idempotent_consumer.process_failed_message_voter'
        );
        $defaultFailedMessageProcessVoterDefinition
            ->setArgument(
                '$wantToProcessFailedMessage',
                $config['process_failed_messages']
            );
        if ($config['custom_process_failed_messages_voter']) {
            $container->setAlias(
                'mms.idempotent_consumer.process_failed_message_voter',
                $config['custom_process_failed_messages_voter']
            );
        }
    }
}
