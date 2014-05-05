<?php

namespace SensioLabs\JobBoardBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SensioLabsJobBoardExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('sensio_labs_job_board.admin_email', $config['admin_email']);
        $container->setParameter('sensio_labs_job_board.max_per_page.homepage', $config['max_per_page']['homepage']);
        $container->setParameter('sensio_labs_job_board.max_per_page.manage', $config['max_per_page']['manage']);
        $container->setParameter('sensio_labs_job_board.administrators', $config['administrators']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
