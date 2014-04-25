<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\JobBoardBundle\Entity\Job;

class LoadJobData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 41; $i++) {
            $job = new Job();
            $job
                ->setTitle('Developer '. $i)
                ->setCompany('SensioLabs')
                ->setCity('Paris')
                ->setCountry('FR')
                ->setContractType('Full Time')
                ->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate
                velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                sunt in culpa qui officia deserunt mollit anim id est laborum.'
                )
                ->setContractType('Full Time')
                ->setHowToApply('jobs@sensiolabs.com')
                ->setStatus(Job::STATUS_PUBLISHED)
            ;

            if ($i > 20) {
                $job->setCountry('GB')
                    ->setCity('London')
                    ->setStatus(Job::STATUS_ARCHIVED)
                ;

                if ($i > 30) {
                    $job->setContractType('Internship');
                }
            }

            $manager->persist($job);
        }

        $manager->flush();
    }
}
