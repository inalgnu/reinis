<?php

namespace SensioLabs\JobBoardBundle\Twig;

use SensioLabs\JobBoardBundle\Service\ViewCounter;

class ViewCountExtension extends \Twig_Extension
{
    private $viewCounter;

    public function __construct(ViewCounter $viewCounter)
    {
        $this->viewCounter = $viewCounter;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getTotalViewCount', array($this, 'getTotalViewCount')),
        );
    }

    public function getTotalViewCount($alias, $id)
    {
        $routes = $this->viewCounter->getRoutes($alias, $id);

        $totalViewCount = 0;

        foreach ($routes as $route) {
            $totalViewCount += $this->viewCounter->getViewForRoute($alias, $id, $route);
        }

        return $totalViewCount;
    }

    public function getName()
    {
        return 'view_count_extension';
    }
}
