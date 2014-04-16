<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AnnouncementTest extends WebTestCase
{
    protected function setUp()
    {
        $client = static::createClient();

        $purger = new ORMPurger($client->getContainer()->get('doctrine')->getManager());
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        parent::tearDown();
    }

    public function testResponseIsSuccessful()
    {
        $client = static::createClient();

        $client->request('GET', '/post');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('POST', '/post');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testFormSubmissionAndRedirection()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post');

        $form = $crawler->selectButton('Preview')->form(array(
            'announcement[title]' => 'Developer',
            'announcement[company]' => 'SensioLabs',
            'announcement[country]' => 'FR',
            'announcement[city]' => 'Paris',
            'announcement[contract_type]'=> 'Full Time',
            'announcement[description]' => 'Some description...',
            'announcement[how_to_apply]' => 'jobs@sensiolabs.com',
        ));

        $client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/FR/full-time/developer/preview'));

        $crawler = $client->followRedirect();

        $this->assertRegExp('/Developer/', $crawler->filter('.title')->text());
        $this->assertRegExp('/SensioLabs/', $crawler->filter('.company')->text());
        $this->assertRegExp('/Paris, France/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Full Time/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Some description../', $crawler->filter('.description')->text());
        $this->assertRegExp('/jobs@sensiolabs.com/', $crawler->filter('.how-to-apply')->text());

        $link = $crawler->selectLink('Make changes')->link();

        $client->click($link);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
