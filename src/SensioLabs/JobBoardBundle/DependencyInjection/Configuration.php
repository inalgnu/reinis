<?php

namespace SensioLabs\JobBoardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sensiolabs_jobboard');

        $rootNode
            ->children()
                ->integerNode('max_per_page')
                    ->info('The maximum number of jobs displayed per page')
                    ->defaultValue(10)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
