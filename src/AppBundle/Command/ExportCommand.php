<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends ContainerAwareCommand {

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		$ser = $this->getContainer()->get('AppBundle\Service\Export');
		$type = $input->getOption('type');
		$succes = false;

		$io->title('Exporting data (' . $type . ') [' . date('d/m/Y H\hi') . ']');

		if($type == 'phrases' || $type == 'all')
		{
			$succes = true;
			$io->section('Export des phrases');

			$start = microtime(true);
			$nb = $ser->phrases();
			$nbSec = microtime(true) - $start;

			$io->text($nb . ' en ' . number_format($nbSec, 2, '.', '') . ' secondes');
		}

		if($type == 'motsAmbigus' || $type == 'all')
		{
			$succes = true;
			$io->section('Export des mots ambigus');

			$start = microtime(true);
			$nb = $ser->motsAmbigus();
			$nbSec = microtime(true) - $start;

			$io->text($nb . ' en ' . number_format($nbSec, 2, '.', '') . ' secondes');
		}

		if(!$succes)
		{
			$io->error([
				'Type (' . $type . ') invalid.',
				'Valid types : all, phrases, motsAmbigus',
			]);
		}
		else
		{
			$io->success('Data (' . $type . ') was successfully exporting.');
		}
	}

    protected function configure () {
        // On set le nom de la commande
	    $this->setName('app:export:generate');

        // On set la description
	    $this->setDescription("Exporting data");

        // On set l'aide
	    $this->setHelp("TYPE : all, phrases, motsAmbigus\nAller sur ExportCommand.php pour plus d'infos");

	    // On set une option
	    $this->addOption(
		    'type',
		    't',
		    InputOption::VALUE_REQUIRED,
		    'Export type',
		    'all'
	    );
    }


}
