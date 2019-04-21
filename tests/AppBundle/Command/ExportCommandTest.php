<?php

namespace Tests\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ExportCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:export:generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName(),
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Data (all) was successfully exporting.', $output);
    }

    public function testExecuteAll()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:export:generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName(),
            '--type' => 'all',
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Data (all) was successfully exporting.', $output);
    }

    public function testExecutePhrases()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:export:generate');
        $commandTester = new CommandTester($command);

        // phrases
        $commandTester->execute([
            'command'  => $command->getName(),
            '--type' => 'phrases',
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Data (phrases) was successfully exporting.', $output);
    }

    public function testExecuteMotsAmbigus()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:export:generate');
        $commandTester = new CommandTester($command);

        // motsAmbigus
        $commandTester->execute([
            'command'  => $command->getName(),
            '--type' => 'motsAmbigus',
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Data (motsAmbigus) was successfully exporting.', $output);
    }

    public function testExecuteBad()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:export:generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName(),
            '--type' => 'foo',
        ]);
        $output = $commandTester->getDisplay();
        $this->assertContains('[ERROR]', $output);
    }
}