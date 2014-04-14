<?php

namespace SensioLabs\JobBoardBundle\TestFunctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class JobControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/post');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', '/post');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
