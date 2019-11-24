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

		$em = $this->getContainer()->get('doctrine')->getManager();
		$membreRepo = $em->getRepository('AppBundle:Membre');

        $type = $input->getOption('type');

        if($type == 'monthly')
        {
            $membreRepo->resetPointsMensuel();

            $io->success('Monthly ranking was successfully resetting.');
        }

        if ($type == 'weekly')
        {
            $membreRepo->resetPointsHebdomadaire();

            $io->success('Weekly ranking was successfully resetting.');
        }

        if ($type == 'both')
        {
            $membreRepo->resetPointsHebdomadaireMensuel();

            $io->success('Monthly and weekly rankings have been successfully reset.');
        }

	}

    protected function configure () {
        // On set le nom de la commande
	    $this->setName('app:points:reinit');

        // On set la description
	    $this->setDescription("Resetting members' ranking points");

        // On set l'aide
	    $this->setHelp("TYPE : both, weekly, monthly\nResetting the members' points to 0");

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
