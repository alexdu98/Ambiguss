<?php

namespace AmbigussBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends ContainerAwareCommand {

    protected function configure () {
        // On set le nom de la commande
        $this->setName('app:uploadData');

        // On set la description
        $this->setDescription("Permet de récupérer toutes les données de la BD sous format JSON");

        // On set l'aide
        $this->setHelp("Aller sur ExportCommand.php pour plus d'infos");


    }

    public function execute (InputInterface $input,OutputInterface $output) {

        $ser=$this->getContainer()->get('ambiguss_export');
        // ne fonctionnent pas
        $ser->downloadAction();
        $ser->MAdownloadAction();
        $output->writeln('Upload done !');
    }


}
