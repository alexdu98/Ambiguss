<?php

namespace App\Command;

use App\Event\GameEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\GenericEvent;

class ReinitialisationPointsCommand extends ContainerAwareCommand {

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);

		$ed = $this->getContainer()->get('event_dispatcher');
		$em = $this->getContainer()->get('doctrine')->getManager();
		$membreRepo = $em->getRepository('App:Membre');
        $type = $input->getOption('type');
        $succes = false;

        $io->title('Resetting ranking points (' . $type . ') [' . date('d/m/Y H\hi') . ']');

        if($type == 'monthly' || $type == 'all')
        {
            $succes = true;
            $io->section('Réinitialisation des points mensuels');

            // Calcul la durée que prend l'historisation et la réinitialisation
            $start = microtime(true);
            $membresMonthly = $membreRepo->getAllWithPointsMensuel();
            $this->historize('monthly', $membresMonthly);
            $membreRepo->resetPointsMensuel();
            $event = new GenericEvent($type, array(
                'membres' => $membresMonthly,
                'type' => $type
            ));
            $ed->dispatch(GameEvents::REINITIALISATION_POINTS, $event);
            $nbSec = microtime(true) - $start;

            $io->text(count($membresMonthly) . ' en ' . number_format($nbSec, 2, '.', '') . ' secondes');
        }

        if ($type == 'weekly' || $type == 'all')
        {
            $succes = true;
            $io->section('Réinitialisation des points hebdomadaires');

            // Calcul la durée que prend l'historisation et la réinitialisation
            $start = microtime(true);
            $membresWeekly = $membreRepo->getAllWithPointsHebdomadaire();
            $this->historize('weekly', $membresWeekly);
            $membreRepo->resetPointsHebdomadaire();
            $event = new GenericEvent($type, array(
                'membres' => $membresWeekly,
                'type' => $type
            ));
            $ed->dispatch(GameEvents::REINITIALISATION_POINTS, $event);
            $nbSec = microtime(true) - $start;

            $io->text(count($membresWeekly) . ' en ' . number_format($nbSec, 2, '.', '') . ' secondes');
        }

        $em->flush();

        if(!$succes)
        {
            $io->error([
                'Type (' . $type . ') invalid.',
                'Valid types : weekly, monthly, all',
            ]);
        }
        else
        {
            $io->success('Ranking (' . $type . ') have been successfully reset.');
        }
	}

    protected function configure() {
        // On set le nom de la commande
	    $this->setName('app:points:reinit');

        // On set la description
	    $this->setDescription("Resetting members' ranking points");

        // On set l'aide
	    $this->setHelp("TYPE : weekly, monthly, all\nResetting the members' points to 0");

        // On set une option
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Reset type'
        );
    }

    private function historize($type, $membres) {
        $historiqueService = $this->getContainer()->get('App\Service\HistoriqueService');

        $typeFR = '';
        $method = '';
        if ($type == 'monthly') {
            $typeFR = 'mensuels';
            $method = 'getPointsClassementMensuel';
        }
        else if ($type == 'weekly') {
            $typeFR = 'hebdomaires';
            $method = 'getPointsClassementHebdomadaire';
        }

        $nbJoueurs = count($membres);
        foreach ($membres as $key => $membre) {
            $msg = 'Réinitialisation des points ' . $typeFR . '. Position : ' . ($key + 1) . '/' . $nbJoueurs . ' (' . $membre->{$method}() . ' points).';
            $historiqueService->save($membre, $msg);
        }
    }

}
