<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;

class AnnouncementTest extends WebTestCase
{
    protected $container;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function testResponseIsSuccessful()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $crawler = $client->request('POST', '/post');

        $this->assertTrue($client->getResponse()->isSuccessful());
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
            'announcement[contract_type]'=> 1,
            'announcement[description]' => 'Some description...',
            'announcement[how_to_apply]' => 'jobs@sensiolabs.com',
        ));

        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/FR/full-time/developer/preview'));

        $client->followRedirect();

        $this->assertRegExp('/Developer/', $client->getResponse()->getContent());
        $this->assertRegExp('/SensioLabs/', $client->getResponse()->getContent());
        $this->assertRegExp('/FR/', $client->getResponse()->getContent());
        $this->assertRegExp('/Paris/', $client->getResponse()->getContent());
        $this->assertRegExp('/Full Time/', $client->getResponse()->getContent());
        $this->assertRegExp('/Some description../', $client->getResponse()->getContent());
        $this->assertRegExp('/jobs@sensiolabs.com/', $client->getResponse()->getContent());
    }

    protected function tearDown()
    {
        $purger = new ORMPurger($this->container->get('doctrine')->getManager());
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        parent::tearDown();
    }
}
