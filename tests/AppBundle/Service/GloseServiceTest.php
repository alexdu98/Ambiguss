<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\GloseService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GloseServiceTest extends KernelTestCase
{
    public function testIsCreatable()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(GloseService::class);

        $coutParGlose = $container->getParameter('costCreateGloseByGlosesOfMotAmbigu');

        $this->assertTrue($service->isCreatable(0, 0));
        $this->assertTrue($service->isCreatable(1, 0));

        $this->assertFalse($service->isCreatable(2, 0));
        $this->assertFalse($service->isCreatable(2, $coutParGlose));

        $nbGloses = 2;
        $this->assertTrue($service->isCreatable($nbGloses, $coutParGlose * $nbGloses));
    }

    public function testGetCostCreate()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $service = $container->get(GloseService::class);

        $coutParGlose = $container->getParameter('costCreateGloseByGlosesOfMotAmbigu');

        $this->assertEquals(0, $service->getCostCreate(0));
        $this->assertEquals(0, $service->getCostCreate(1));

        $nbGloses = 2;
        $this->assertEquals($nbGloses * $coutParGlose, $service->getCostCreate($nbGloses));
    }

}
