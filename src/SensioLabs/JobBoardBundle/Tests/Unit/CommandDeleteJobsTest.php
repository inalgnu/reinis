<?php

namespace SensioLabs\JobBoardBundle\Test\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use SensioLabs\JobBoardBundle\Entity\Job;
use Symfony\Component\Process\Process;

class CommandDeleteJobs extends WebTestCase
{
    protected $em;

    protected function setUp()
    {
        $kernel = static::createClient();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $purger = new ORMPurger($kernel->getContainer()->get('doctrine.orm.entity_manager'));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();
    }

    public function testCommand()
    {
        $now = new \Datetime();
        $job = new Job();
        $job
            ->setTitle('Not deleted')
            ->setCompany('SensioLabs')
            ->setCity('Paris')
            ->setCountry('FR')
            ->setContractType('Full Time')
            ->setDescription('Lorem ipsum dolor sit amet')
            ->setContractType('Full Time')
            ->setStatus(Job::STATUS_DELETED)
            ->setDeletedAt($now->modify('-19 day'))
        ;

        $this->em->persist($job);
        $this->em->flush();

        $process = new Process('php app/console jobboard:delete --env=test');
        $process->run();

        $job = $this->em->getRepository('SensioLabsJobBoardBundle:Job')->findOneByTitle('Not deleted');
        $this->assertNotNull($job);

        $job = new Job();
        $job
            ->setTitle('deleted')
            ->setCompany('SensioLabs')
            ->setCity('Paris')
            ->setCountry('FR')
            ->setContractType('Full Time')
            ->setDescription('Lorem ipsum dolor sit amet')
            ->setContractType('Full Time')
            ->setStatus(Job::STATUS_DELETED)
            ->setDeletedAt($now->modify('-25 day'))
        ;

        $this->em->persist($job);
        $this->em->flush();

        $process->run();

        $job = $this->em->getRepository('SensioLabsJobBoardBundle:Job')->findOneByTitle('deleted');
        $this->assertNull($job);
    }
}
