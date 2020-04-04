<?php

namespace Tests\AppBundle\Service;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\Warning;
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
        $tag = $this->service->getLastDev();

        $this->assertInstanceOf(stdClass::class, $tag);
    }

    public function testGetActualCommit()
    {
        $tag = $this->service->getActualCommit();

        try {
            $this->assertInstanceOf(stdClass::class, $tag);
        }
        catch (ExpectationFailedException $e) {
            throw new Warning('Le tag de la version actuelle (' . $this->container->getParameter('version') . ') n\'existe pas.');
        }
    }

}
