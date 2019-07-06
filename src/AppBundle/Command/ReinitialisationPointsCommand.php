<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReinitialisationPointsCommand extends ContainerAwareCommand {

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		$dco = $this->getContainer()->get('doctrine')->getConnection();
        $type = $input->getOption('type');

        if($type == 'monthly')
        {
            $sql = 'UPDATE membre SET points_classement_mensuel = 0;';
            $query = $dco->prepare($sql);
            $query->execute();

            $io->success('Monthly ranking was successfully resetting.');
        }

        if ($type == 'weekly')
        {
            $sql = 'UPDATE membre SET points_classement_hebdomadaire = 0;';
            $query = $dco->prepare($sql);
            $query->execute();

            $io->success('Weekly ranking was successfully resetting.');
        }

        if ($type == 'both')
        {
            $sql = 'UPDATE membre SET points_classement_mensuel = 0, points_classement_hebdomadaire = 0;';
            $query = $dco->prepare($sql);
            $query->execute();

            $io->success('Monthly and weekly rankings have been successfully reset.');
        }

	}

    protected function configure () {
        // On set le nom de la commande
	    $this->setName('app:points:reinit');

        // On set la description
	    $this->setDescription("Resetting members' ranking points");

        // On set l'aide
	    $this->setHelp("Resetting the members' points to 0");

        // On set une option
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Reset type',
            'both'
        );
    }

}
