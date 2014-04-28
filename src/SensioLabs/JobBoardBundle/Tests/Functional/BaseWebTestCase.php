<?php

namespace SensioLabs\JobBoardBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUserAndJobData;

abstract class BaseWebTestCase extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    protected function loadFixtures()
    {
        $em = $this->client->getContainer()->get('doctrine')->getManager();

        $loader = new Loader();
        $fixture = new LoadUserAndJobData();
        $fixture->setContainer($this->client->getContainer());
        $loader->addFixture($fixture);
        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);

        $executor->execute($loader->getFixtures());
    }

    protected function purge()
    {
        $purger = new ORMPurger($this->client->getContainer()->get('doctrine')->getManager());
        $purger->purge();
    }
}
