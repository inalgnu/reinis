<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();

        $purger = new ORMPurger($this->client->getContainer()->get('doctrine.orm.entity_manager'));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        $this->loadFixtures(array('SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUserData'));
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function testFormSubmissionAndRedirection()
    {
        $this->client->request('GET', '/manage');

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $form = $crawler->selectButton('Login')->form(array(
            '_username' => 'skigun',
            '_password' => 'password',
        ));

        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
