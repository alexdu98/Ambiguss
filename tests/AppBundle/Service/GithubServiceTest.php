<?php

namespace Tests\AppBundle\Service;

use \stdClass;
use AppBundle\Service\GithubService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GithubServiceTest extends KernelTestCase
{
    private $container;
    private $service;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->container = $kernel->getContainer();
        $this->service = $this->container->get(GithubService::class);
    }

    public function testGetLastDev()
    {
        $game = $this->service->getLastDev();

        $this->assertInstanceOf(stdClass::class, $game);
    }

    public function testGetActualCommit()
    {
        $game = $this->service->getActualCommit();

        $this->assertInstanceOf(stdClass::class, $game);
    }

}
