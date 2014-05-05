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
                ->arrayNode('administrators')
                    ->info('Array of administrator\'s uuids')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->ArrayNode('max_per_page')
                    ->children()
                        ->integerNode('homepage')
                            ->info('The maximum number of jobs displayed per page in the homepage route')
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('manage')
                            ->info('The maximum number of jobs displayed per page in the manage route')
                            ->defaultValue(25)
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('admin_email')
                    ->info('The email address of the administrator')
                    ->isRequired()
                    ->validate()
                    ->ifTrue(function ($s) {
                        return preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$#', $s) !== 1;
                    })
                    ->thenInvalid('Invalid email')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
