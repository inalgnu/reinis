<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;

class AnnouncementTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->client = static::createClient();
    }

    public function testResponseIsSuccessful()
    {
        $this->client->request('GET', '/post');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/post');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testFormSubmissionAndRedirection()
    {
        $crawler = $this->client->request('GET', '/post');

        $form = $crawler->selectButton('Preview')->form(array(
            'announcement[title]' => 'Developer',
            'announcement[company]' => 'SensioLabs',
            'announcement[country]' => 'FR',
            'announcement[city]' => 'Paris',
            'announcement[contract_type]'=> 1,
            'announcement[description]' => 'Some description...',
            'announcement[how_to_apply]' => 'jobs@sensiolabs.com',
        ));

        $this->client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/full-time/developer/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Developer/', $crawler->filter('.title')->text());
        $this->assertRegExp('/SensioLabs/', $crawler->filter('.company')->text());
        $this->assertRegExp('/Paris, France/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Full Time/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Some description../', $crawler->filter('.description')->text());
        $this->assertRegExp('/jobs@sensiolabs.com/', $crawler->filter('.how-to-apply')->text());

        $link = $crawler->selectLink('Make changes')->link();

        $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown()
    {
        $purger = new ORMPurger($this->client->getContainer()->get('doctrine')->getManager());
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        parent::tearDown();
    }
}
