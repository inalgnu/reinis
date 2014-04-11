<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post');

        $this->assertTrue($client->getResponse()->isSuccessful());

        $crawler = $client->request('POST', '/post');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
