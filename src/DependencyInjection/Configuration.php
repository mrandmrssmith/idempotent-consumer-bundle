<?php


namespace MrAndMrsSmith\IdempotentConsumerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mms_idempotent_consumer');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->booleanNode('process_failed_messages')
                ->defaultValue(false)
                ->end()
            ->scalarNode('custom_process_failed_messages_voter')
                ->defaultNull()
                ->end()
        ->end();

        return $treeBuilder;
    }
}
