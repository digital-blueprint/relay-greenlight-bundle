<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dbp_relay_greenlight');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('database_url')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
