<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use SensioLabs\JobBoardBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Process\Process;

class ElasticSearchTest extends WebTestCase
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

        $process = new Process('php app/console fos:elastica:populate --env=test');
        $process->run();
    }

    public function testSearch()
    {
        $this->client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/?q=developer+10');
        $this->assertCount(10, $crawler->filter('.box'));

        $firstJob = $crawler->filter('.box')->first();
        $this->assertRegExp('/Developer 10/', $firstJob->text());

        $crawler = $this->client->request('GET', '/?q=microsoft');
        $this->assertCount(5, $crawler->filter('.box'));
        $this->assertRegExp('/United Kingdom \(5\)/', $crawler->filter('.filter')->text());

        $crawler = $this->client->request('GET', '/?q=london+microsoft');
        $this->assertCount(10, $crawler->filter('.box'));

        $firstJob = $crawler->filter('.box')->first();
        $this->assertRegExp('/Microsoft/', $firstJob->text());
        $this->assertRegExp('/London/', $firstJob->text());

        $crawler = $this->client->request('GET', '/?q=microsoft+best+38');
        $this->assertCount(10, $crawler->filter('.box'));

        $firstJob = $crawler->filter('.box')->first();
        $this->assertRegExp('/Microsoft/', $firstJob->text());
        $this->assertRegExp('/Best Developer 38/', $firstJob->text());
    }
}
