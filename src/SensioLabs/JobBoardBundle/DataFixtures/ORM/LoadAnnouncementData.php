<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\JobBoardBundle\Entity\Announcement;

class LoadAnnouncementData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 41; $i++) {
            $announcement = new Announcement();
            $announcement
                ->setTitle('Developer '. $i)
                ->setCompany('SensioLabs')
                ->setCity('Paris')
                ->setCountry('FR')
                ->setContractType('Full Time')
                ->setDescription('lorem ipsum, bla bla bla...')
                ->setContractType('Full Time')
                ->setHowToApply('jobs@sensiolabs.com')
                ->setStatus(Announcement::STATUS_SAVED)
            ;

            if ($i > 20) {
                $announcement->setCountry('GB');

                if ($i > 30) {
                    $announcement->setContractType('Internship');
                }
            }

            $manager->persist($announcement);
        }

        $manager->flush();
    }
}
