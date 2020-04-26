<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\PhraseService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhraseServiceTest extends KernelTestCase
{
    public function testIsCreatable()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(PhraseService::class);

        $coutParMA = $container->getParameter('costCreatePhraseByMotAmbiguCredits');

        $this->assertTrue($service->isCreatable(0, 0));
        $this->assertTrue($service->isCreatable(1, $coutParMA));
        $this->assertTrue($service->isCreatable(1, $coutParMA + 1));

        $this->assertFalse($service->isCreatable(1, 0));
        $this->assertFalse($service->isCreatable(1, $coutParMA - 1));
    }

    public function testGetPrice()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(PhraseService::class);

        $coutParMA = $container->getParameter('costCreatePhraseByMotAmbiguCredits');

        $this->assertEquals(0 * $coutParMA, $service->getPrice(0));
        $this->assertEquals(1 * $coutParMA, $service->getPrice(1));
    }

}
