<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\ExportService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExportServiceTest extends KernelTestCase
{
    public function testPhrases()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(ExportService::class, 'phrases');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(ExportService::class);
        
        $res = $method->invoke($service);
        
        $this->assertArrayHasKey('phrase', $res[0]);
        $this->assertContains('<amb id="1">', $res[0]['phrase']);

        $this->assertArrayHasKey('motsAmbigus', $res[0]);
        $this->assertArrayHasKey('motAmbigu', $res[0]['motsAmbigus'][0]);
        $this->assertArrayHasKey('ordre', $res[0]['motsAmbigus'][0]);
        $this->assertArrayHasKey('nbRep', $res[0]['motsAmbigus'][0]);
        $this->assertArrayHasKey('gloses', $res[0]['motsAmbigus'][0]);
        $this->assertArrayHasKey('valeur', $res[0]['motsAmbigus'][0]['gloses'][0]);
        $this->assertArrayHasKey('nbRep', $res[0]['motsAmbigus'][0]['gloses'][0]);
    }

    public function testMotsAmbigus()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(ExportService::class, 'motsAmbigus');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(ExportService::class);
        
        $res = $method->invoke($service);
        
        $this->assertArrayHasKey('motAmbigu', $res[0]);
        $this->assertArrayHasKey('gloses', $res[0]);
    }

    public function testSave()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $method = new \ReflectionMethod(ExportService::class, 'save');
        $method->setAccessible(true);

        $container = $kernel->getContainer();
        $service = $container->get(ExportService::class);
        
        $fileName = 'test.json';
        $method->invoke($service, $fileName, array());

        $this->assertFileIsReadable($service->getDownloadDir() . $fileName);
    }
}