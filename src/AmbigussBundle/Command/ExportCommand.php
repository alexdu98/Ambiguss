<?php

namespace AmbigussBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends ContainerAwareCommand {

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$ser = $this->getContainer()->get('ambiguss_export');

		$output->writeln([
			'Export des phrases...',
		]);
		$start = microtime(true);
		$ser->phrasesAction();
		$nbSec = microtime(true) - $start;
		$output->writeln([
			ceil($nbSec) . ' secondes',
			'',
		]);

		$output->writeln([
			'Export des mots ambigus...',
		]);
		$start = microtime(true);
		$ser->motsAmbigusAction();
		$nbSec = microtime(true) - $start;
		$output->writeln([
			ceil($nbSec) . ' secondes',
			'',
		]);

		$output->writeln('Export termine !');
	}

    protected function configure () {
        // On set le nom de la commande
	    $this->setName('app:export:generate');

        // On set la description
	    $this->setDescription("Génère les fichiers d'export");

        // On set l'aide
        $this->setHelp("Aller sur ExportCommand.php pour plus d'infos");
    }


}
