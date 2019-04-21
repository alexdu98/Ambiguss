<?php

namespace Tests\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CanonicalisationCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:member:canonize');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName()
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] All members canonized.', $output);
    }
}