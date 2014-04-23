<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use SensioLabs\JobBoardBundle\DataFixtures\ORM\LoadJobData;

class JobControllerTest extends WebTestCase
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
            'job[title]' => 'Developer',
            'job[company]' => 'SensioLabs',
            'job[country]' => 'FR',
            'job[city]' => 'Paris',
            'job[contractType]'=> 'Internship',
            'job[description]' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit',
            'job[howToApply]' => 'jobs@sensiolabs.com',
        ));

        $this->client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/Internship/developer/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Developer/', $crawler->filter('.title')->text());
        $this->assertRegExp('/SensioLabs/', $crawler->filter('.company')->text());
        $this->assertRegExp('/Paris, France/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Internship/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Lorem ipsum dolor sit amet, consectetuer adipiscing elit/', $crawler->filter('.description')->text());
        $this->assertRegExp('/jobs@sensiolabs.com/', $crawler->filter('.how-to-apply')->text());

        // Check the pre-filled form after clicking on make changes
        $link = $crawler->selectLink('Make changes')->link();

        $crawler = $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form();

        $this->assertEquals('Developer', $form['job[title]']->getValue());
        $this->assertEquals('SensioLabs', $form['job[company]']->getValue());
        $this->assertEquals('FR', $form['job[country]']->getValue());
        $this->assertEquals('Paris', $form['job[city]']->getValue());
        $this->assertEquals('Internship', $form['job[contractType]']->getValue());
        $this->assertEquals('Lorem ipsum dolor sit amet, consectetuer adipiscing elit', $form['job[description]']->getValue());
        $this->assertEquals('jobs@sensiolabs.com', $form['job[howToApply]']->getValue());

        // Update the title and check if the route parameter is correct
        $form['job[title]'] = 'Developer 2';

        $this->client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/Internship/developer-2/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Developer 2/', $crawler->filter('.title')->text());
    }

    public function testJobList()
    {
        $this->loadFixtures();

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertCount(10, $crawler->filter('.box'));
    }

    public function testJobListAjaxCall()
    {
        $this->loadFixtures();

        // We simulate an ajax call in homepage
        $crawler = $this->client->request('GET', '/', array('page' => 2), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertCount(10, $crawler->filter('.box'));
    }

    protected function loadFixtures()
    {
        $fixture = new LoadJobData();
        $fixture->load($this->client->getContainer()->get('doctrine')->getManager());
    }
}
