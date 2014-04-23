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
