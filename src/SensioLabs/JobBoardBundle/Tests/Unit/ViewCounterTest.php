<?php

namespace SensioLabs\JobBoardBundle\Test\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ViewCounterTest extends WebTestCase
{
    protected $viewCounter;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->viewCounter = $kernel->getContainer()->get('sensiolabs.service.view_counter');
    }

    public function testIncrementView()
    {
        $this->viewCounter->incrementViewForRoute('Test', 1, 'homepage');
        $this->viewCounter->incrementViewForRoute('Test', 1, 'homepage');

        $this->assertEquals(2, $this->viewCounter->getViewForRoute('Test', 1, 'homepage'));

        $this->viewCounter->deleteViewForRoute('Test', 1, 'homepage');

        $this->assertNull($this->viewCounter->getViewForRoute('Test', 1, 'homepage'));
    }

    public function testGetRoutes()
    {
        $this->viewCounter->incrementViewForRoute('Test', 1, 'homepage');

        $this->assertSame(array('homepage'), $this->viewCounter->getRoutes('Test', 1));

        $this->viewCounter->incrementViewForRoute('Test', 1, 'job_show');

        $this->assertSame(array('job_show', 'homepage'), $this->viewCounter->getRoutes('Test', 1));

        $this->viewCounter->deleteViewForRoute('Test', 1, 'homepage');
        $this->viewCounter->deleteViewForRoute('Test', 1, 'job_show');

        $this->assertEmpty($this->viewCounter->getRoutes('Test', 1));
    }
}
