<?php

namespace SensioLabs\BackendBundle\Test\Functional;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use SensioLabs\Connect\Security\Authentication\Token\ConnectToken;

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

    public function testJobList()
    {
        $crawler = $this->logIn();

        $this->assertRegExp('/Published ads/', $crawler->filter('.big-title')->text());
        $this->assertCount(25, $crawler->filter('.box tbody tr'));

        $link = $crawler->selectLink('Archived ads')->link();
        $crawler = $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/Archived ads/', $crawler->filter('.big-title')->text());
        $this->assertCount(1, $crawler->filter('.box tbody tr'));

        $link = $crawler->selectLink('Deleted ads')->link();
        $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testJobEdit()
    {
        $crawler = $this->logIn();

        $link = $crawler->selectLink('Edit')->link();
        $crawler = $this->client->click($link);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Update')->form();

        $this->assertNotNull($form['job[title]']->getValue());
        $this->assertNotNull($form['job[company]']->getValue());
        $this->assertNotNull($form['job[country]']->getValue());
        $this->assertNotNull($form['job[city]']->getValue());
        $this->assertNotNull($form['job[contractType]']->getValue());
        $this->assertNotNull($form['job[description]']->getValue());
        $this->assertNotNull($form['job[howToApply]']->getValue());

        $now = new \DateTime();
        $tomorrow = new \DateTime('tomorrow');
        $form['job[visibleFrom]'] = $now->format('Y-m-d');
        $form['job[visibleTo]'] = $tomorrow->format('Y-m-d');

        $this->client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSoftDeleteAndRestore()
    {
        $crawler = $this->logIn();

        $link = $crawler->selectLink('Delete')->link();
        $this->client->click($link);

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/You cannot delete .*, it must not be published./', $crawler->filter('.alert')->text());

        $link = $crawler->selectLink('Archived ads')->link();
        $crawler = $this->client->click($link);

        // There is 1 job Archived
        $this->assertCount(1, $crawler->filter('.box tbody tr'));

        // We delete the job
        $link = $crawler->selectLink('Delete')->link();
        $this->client->click($link);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/Job .* has been successfully deleted./', $crawler->filter('.alert')->text());

        $link = $crawler->selectLink('Deleted ads')->link();
        $crawler = $this->client->click($link);

        // There is 1 job Deleted
        $this->assertCount(1, $crawler->filter('.box tbody tr'));

        // We restore the job
        $link = $crawler->selectLink('Restore')->link();
        $this->client->click($link);

        $crawler = $this->client->followRedirect();

        // He is no more in deleted jobs list
        $this->assertRegExp('/Job .* has been successfully restored./', $crawler->filter('.alert')->text());
        $this->assertCount(0, $crawler->filter('.box tbody tr'));
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $user = $this->getContainer()->get('sensiolabs.user_repository')->loadUserByUsername('cc649fb3-155f-4016-acd4-0e7e01fd57ae');

        $firewall = 'secured_area';
        $token = new ConnectToken($user->getUuid(), null, null, 'toto');
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        return $this->client->request('GET', '/backend');
    }
}
