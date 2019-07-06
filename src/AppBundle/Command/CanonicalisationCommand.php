<?php

namespace AppBundle\Command;

use FOS\UserBundle\Util\Canonicalizer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Style\SymfonyStyle;

class CanonicalisationCommand extends ContainerAwareCommand {

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
		$em = $this->getContainer()->get('doctrine')->getManager();
		$ms = $em->getRepository('AppBundle:Membre')->findAll();
		$c = new Canonicalizer();

		try {
			// Enregistre l'email et le pseudo sous forme canonique pour chaque membre
			foreach($ms as $m){
				$m->setUsernameCanonical($c->canonicalize($m->getUsername()));
				$m->setEmailCanonical($c->canonicalize($m->getEmail()));
				$em->persist($m);
				$em->flush();
			}
			$io->success('All members canonized.');
		}
		catch(Exception $e) {
			$io->error([
				$e->getCode() . ' : ' . $e->getMessage(),
			]);
		}
	}

    protected function configure () {
        // On set le nom de la commande
	    $this->setName('app:member:canonize');

        // On set la description
	    $this->setDescription("Canonilizing members");

        // On set l'aide
	    $this->setHelp("Set field username_canonical and email_canonical for all members");
    }

}
