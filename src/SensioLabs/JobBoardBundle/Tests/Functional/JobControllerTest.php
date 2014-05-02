<?php

namespace SensioLabs\JobBoardBundle\Test\Functional;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Process\Process;

class JobControllerTest extends WebTestCase
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

    protected function elasticRefresh()
    {
        $process = new Process('php app/console fos:elastica:populate --env=test');
        $process->run();
    }

    public function testResponseIsSuccessful()
    {
        $this->client->request('GET', '/post');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/post');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testFormSubmissionAndRedirection()
    {
        $crawler = $this->client->request('GET', '/post');

        $form = $crawler->selectButton('Preview')->form(array(
            'job[title]' => 'Stage',
            'job[company]' => 'SensioLabs',
            'job[country]' => 'FR',
            'job[city]' => 'Paris',
            'job[contractType]'=> 'Internship',
            'job[description]' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit',
            'job[howToApply]' => 'jobs@sensiolabs.com',
        ));

        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/Internship/stage/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Stage/', $crawler->filter('.title')->text());
        $this->assertRegExp('/SensioLabs/', $crawler->filter('.company')->text());
        $this->assertRegExp('/Paris, France/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Internship/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Lorem ipsum dolor sit amet, consectetuer adipiscing elit/', $crawler->filter('.description')->text());
        $this->assertRegExp('/jobs@sensiolabs.com/', $crawler->filter('.how-to-apply')->text());

        // Check the pre-filled form after clicking on make changes
        $link = $crawler->selectLink('Make changes')->link();

        $crawler = $this->client->click($link);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form();

        $this->assertSame('Stage', $form['job[title]']->getValue());
        $this->assertSame('SensioLabs', $form['job[company]']->getValue());
        $this->assertSame('FR', $form['job[country]']->getValue());
        $this->assertSame('Paris', $form['job[city]']->getValue());
        $this->assertSame('Internship', $form['job[contractType]']->getValue());
        $this->assertSame('Lorem ipsum dolor sit amet, consectetuer adipiscing elit', $form['job[description]']->getValue());
        $this->assertSame('jobs@sensiolabs.com', $form['job[howToApply]']->getValue());

        // Update the title and check if the route parameter is correct
        $form['job[title]'] = 'Stage 2';

        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect('/FR/Internship/stage-2/preview'));

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Stage 2/', $crawler->filter('.title')->text());
    }

    public function testJobList()
    {
        $this->elasticRefresh();
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertCount(10, $crawler->filter('.box'));
    }

    public function testJobListAjaxCall()
    {
        $this->elasticRefresh();

        // We simulate an ajax call in homepage
        $crawler = $this->client->request('GET', '/', array('page' => 2), array(), array(
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ));

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertCount(10, $crawler->filter('.box'));
    }

    public function testJobShow()
    {
        $this->elasticRefresh();
        $crawler = $this->client->request('GET', '/');
        $link = $crawler->selectLink('Developer 1')->link();
        $crawler = $this->client->click($link);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/Developer 1/', $crawler->filter('.title')->text());
        $this->assertRegExp('/SensioLabs/', $crawler->filter('.company')->text());
        $this->assertRegExp('/Paris, France/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Full Time/', $crawler->filter('.details')->text());
        $this->assertRegExp('/Lorem ipsum dolor sit amet, consectetur adipisicing elit/', $crawler->filter('.description')->text());
        $this->assertRegExp('/jobs@sensiolabs.com/', $crawler->filter('.how-to-apply')->text());
    }

    public function testFeed()
    {
        $this->client->request('GET', '/rss');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $jobs = new \SimpleXMLElement($this->client->getResponse()->getContent());
        $this->assertEquals('Jobs SensioLabs', $jobs->channel->title);
    }
}
