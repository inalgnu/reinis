<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadJobData;

class ApiTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();

        $purger = new ORMPurger($this->client->getContainer()->get('doctrine.orm.entity_manager'));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();
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
        $this->loadFixtures();
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

        $this->assertSame('Developer 1', $jobApi['title']);
        $this->assertSame('Paris', $jobApi['city']);
        $this->assertSame('SensioLabs', $jobApi['company']);
        $this->assertSame('FR', $jobApi['country_code']);
        $this->assertSame('France', $jobApi['country_name']);
        $this->assertSame('Full Time', $jobApi['contract']);
        $this->assertSame('/FR/Full%20Time/developer-1', $jobApi['url']);
    }

    protected function loadFixtures()
    {
        $fixture = new LoadJobData();
        $fixture->load($this->client->getContainer()->get('doctrine')->getManager());
    }
}
