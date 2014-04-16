<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AnnouncementTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();

        $purger = new ORMPurger($this->client->getContainer()->get('doctrine.orm.entity_manager'));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }

    protected function tearDown()
    {
        $this->client = null;
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
            'announcement[contract_type]'=> 'Full Time',
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

        // Check the pre-filled form after clicking on make changes
        $link = $crawler->selectLink('Make changes')->link();

        $crawler = $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form();

        $this->assertEquals('Developer', $form['announcement[title]']->getValue());
        $this->assertEquals('SensioLabs', $form['announcement[company]']->getValue());
        $this->assertEquals('FR', $form['announcement[country]']->getValue());
        $this->assertEquals('Paris', $form['announcement[city]']->getValue());
        $this->assertEquals('Full Time', $form['announcement[contract_type]']->getValue());
        $this->assertEquals('Some description...', $form['announcement[description]']->getValue());
        $this->assertEquals('jobs@sensiolabs.com', $form['announcement[how_to_apply]']->getValue());

        // Update the title and check if the route parameter is correct
        $form['announcement[title]'] = 'Developer 2';

        $this->client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/full-time/developer-2/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Developer 2/', $crawler->filter('.title')->text());
    }
}
