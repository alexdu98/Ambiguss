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

        $autoType = null;
        if ($type == 'auto')
        {
            $isMonday = date('N') == 1; // 1 : monday
            $isTheFirst = date('d') == 1; // 1 : first day of month

            if ($isMonday && $isTheFirst)
                $autoType = 'both';
            else if ($isMonday)
                $autoType = 'weekly';
            else if ($isTheFirst)
                $autoType = 'monthly';
        }

        if($type == 'monthly' || $autoType == 'monthly')
        {
            $membreRepo->resetPointsMensuel();

            $io->success('Monthly ranking was successfully resetting.');
        }

        if ($type == 'weekly' || $autoType == 'weekly')
        {
            $membreRepo->resetPointsHebdomadaire();

            $io->success('Weekly ranking was successfully resetting.');
        }

        if ($type == 'both' || $autoType == 'both')
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
	    $this->setHelp("TYPE : auto, weekly, monthly, both\nResetting the members' points to 0");

        // On set une option
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Reset type',
            'auto'
        );
    }

}
