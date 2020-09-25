<?php

namespace Symfony\Bundle\LiveTwigBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('live_twig');
        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('mercure_public_url')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
