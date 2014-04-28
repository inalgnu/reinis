<?php

namespace SensioLabs\JobBoardBundle\Test\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SensioLabs\JobBoardBundle\Entity\Job;

class JobListener extends WebTestCase
{
    private $em;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCompanyAndLocationConnected()
    {
        for ($i = 1; $i < 10; $i++) {
            $job = new Job();
            $job
                ->setTitle('Developer '. $i)
                ->setCompany('SensioLabs')
                ->setCity('Paris')
                ->setCountry('FR')
                ->setContractType('Full Time')
                ->setDescription('Lorem ipsum dolor sit amet')
                ->setContractType('Full Time')
                ->setHowToApply('jobs@sensiolabs.com')
                ->setStatus(Job::STATUS_PUBLISHED)
            ;

            $this->em->persist($job);
            $this->em->flush();
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('count(location.id)');
        $qb->from('SensioLabsJobBoardBundle:Location', 'location');
        $rowLocation = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $rowLocation);

        $qb = $this->em->createQueryBuilder();
        $qb->select('count(company.id)');
        $qb->from('SensioLabsJobBoardBundle:Company', 'company');
        $rowCompany = $qb->getQuery()->getSingleScalarResult();

        $this->assertEquals(1, $rowCompany);
    }
}
