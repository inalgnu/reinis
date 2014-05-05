<?php

namespace SensioLabs\JobBoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SensioLabs\JobBoardBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User('cc649fb3-155f-4016-acd4-0e7e01fd57ae');
        $user->setUsername('shima5');
        $user->setName('Grinbergs Reinis');
        $user->setEmail('rpg600@hotmail.com');
        $user->setRoles(array(User::ROLE_ADMIN));

        $this->addReference('user-1', $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
