<?php

namespace SensioLabs\JobBoardBundle\DataTransformer;

use FOS\ElasticaBundle\Doctrine\ORM\ElasticaToModelTransformer;
use Doctrine\ORM\Query;

class JobModelTransformer extends ElasticaToModelTransformer
{
    protected function findByIdentifiers(array $identifierValues, $hydrate)
    {
        if (empty($identifierValues)) {
            return array();
        }
        $hydrationMode = $hydrate ? Query::HYDRATE_OBJECT : Query::HYDRATE_ARRAY;

        $qb = $this->getEntityQueryBuilder();

        $qb->where($qb->expr()->in(static::ENTITY_ALIAS.'.'.$this->options['identifier'], ':values'))
            ->setParameter('values', $identifierValues)
            ->addSelect('l, c')
            ->leftJoin(self::ENTITY_ALIAS.'.location', 'l')
            ->leftJoin(self::ENTITY_ALIAS.'.firm', 'c')
        ;

        return $qb->getQuery()->setHydrationMode($hydrationMode)->execute();
    }
}
