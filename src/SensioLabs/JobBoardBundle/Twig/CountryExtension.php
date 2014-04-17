<?php

namespace SensioLabs\JobBoardBundle\Twig;

use Symfony\Component\Intl\Intl;

class CountryExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('country', array($this, 'countryCodeConverter')),
        );
    }

    public function countryCodeConverter($countryCode)
    {
        return Intl::getRegionBundle()->getCountryName($countryCode);
    }

    public function getName()
    {
        return 'sensiolabs_country_extension';
    }
}
