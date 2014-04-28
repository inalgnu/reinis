<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();

        $purger = new ORMPurger($this->client->getContainer()->get('doctrine.orm.entity_manager'));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        $this->loadFixtures(array(
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadUserData',
            'SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadJobData'
        ));
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function testRedirect()
    {
        $this->client->request('GET', '/api/random');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/'));
    }

    public function testApiRandomJob()
    {
        $this->client->request(
            'GET',
            '/api/random',
            array(),
            array(),
            array('HTTP_HOST' => 'wrong_domain.com')
        );
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

        $this->client->request(
            'GET',
            '/api/random',
            array(),
            array(),
            array('HTTP_HOST' => 'symfony.com')
        );
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $jsonResponse = $this->client->getResponse()->getContent();
        $jobApi = json_decode($jsonResponse, true);

        $this->assertNotNull($jobApi['title']);
        $this->assertNotNull($jobApi['city']);
        $this->assertNotNull($jobApi['company']);
        $this->assertNotNull($jobApi['country_code']);
        $this->assertNotNull($jobApi['country_name']);
        $this->assertNotNull($jobApi['contract']);
        $this->assertNotNull($jobApi['url']);
    }
}
