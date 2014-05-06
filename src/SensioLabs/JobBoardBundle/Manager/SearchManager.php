<?php

namespace SensioLabs\JobBoardBundle\Manager;

use FOS\ElasticaBundle\DynamicIndex;
use FOS\ElasticaBundle\Doctrine\RepositoryManager;

class SearchManager
{
    private $finder;

    private $repositoryManager;

    public function __construct(DynamicIndex $finder, RepositoryManager $repositoryManager)
    {
        $this->finder = $finder;
        $this->repositoryManager = $repositoryManager;
    }

    public function getJobs($page, $maxResults, $country, $contractType, $searchText = null)
    {
        $query = new \Elastica\Query();

        if (empty($searchText)) {
            $queryPart = new \Elastica\Query\MatchAll();
            $query->setSort(array('created_at' => array('order' => 'desc')));
        } else {
            $queryPart = new \Elastica\Query\Bool();
            $matchTitle = new \Elastica\Query\Match();
            $matchTitle->setFieldQuery('title', $searchText)->setFieldBoost('title', 3);
            $matchDescription = new \Elastica\Query\Match();
            $matchDescription->setFieldQuery('description', $searchText);
            $matchCompany = new \Elastica\Query\Match();
            $matchCompany->setFieldQuery('company', $searchText)->setFieldBoost('company', 2);
            $matchCity = new \Elastica\Query\Match();
            $matchCity->setFieldQuery('city', $searchText)->setFieldBoost('city', 1);

            $queryPart->addShould($matchTitle);
            $queryPart->addShould($matchDescription);
            $queryPart->addShould($matchCompany);
            $queryPart->addShould($matchCity);
        }

        $filters = new \Elastica\Filter\Bool();

        if ($country) {
            $filters->addMust(new \Elastica\Filter\Term(array('country' => $country)));
        }

        if ($contractType) {
            $match = new \Elastica\Query\Match();
            $filters->addMust(new \Elastica\Filter\Query($match->setFieldQuery('contractType', $contractType)));
        }

        $query->setQuery(new \Elastica\Query\Filtered($queryPart, $filters));

        $countryFacet = new \Elastica\Facet\Terms('country');
        $countryFacet->setField('country');
        $query->addFacet($countryFacet);

        $contractTypeFacet = new \Elastica\Facet\Terms('contractType');
        $contractTypeFacet->setField('contractType');
        $query->addFacet($contractTypeFacet);

        $query->setFrom(($page - 1) * $maxResults);
        $query->setSize($maxResults);

        $result = $this->finder->search($query);
        $facets = $result->getFacets();

        $searchRepository = $this->repositoryManager->getRepository('SensioLabsJobBoardBundle:Job');
        $jobs = $searchRepository->find($query);

        $countryCount = array();
        $contractTypeCount = array();

        foreach ($facets['country']['terms'] as $facet) {
            $countryCount[$facet['term']] = $facet['count'];
        }

        foreach ($facets['contractType']['terms'] as $facet) {
            $contractTypeCount[$facet['term']] = $facet['count'];
        }

        return array(
            'jobs'          => $jobs,
            'country'       => $countryCount,
            'contractType'  => $contractTypeCount,
        );
    }
}
