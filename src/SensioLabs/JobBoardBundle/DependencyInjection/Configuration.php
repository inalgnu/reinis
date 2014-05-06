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
                    ->info('Set administrator\'s uuids')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($value) {
                            return array($value);
                        })
                    ->end()
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('api_host_granted')
                    ->info('Set granted hosts')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($value) {
                            return array($value);
                        })
                    ->end()
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
                    ->ifTrue(function ($value) {
                        return !filter_var($value, FILTER_VALIDATE_EMAIL);
                    })
                    ->thenInvalid('Invalid email')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
