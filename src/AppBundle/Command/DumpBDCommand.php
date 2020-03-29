<?php

namespace AppBundle\Command;

use AppBundle\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumpBDCommand extends ContainerAwareCommand
{
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);
        $container = $this->getContainer();
        $dumpEmail = $container->getParameter('dump_email');
        $dumpDir = $container->getParameter('dump_dir');
        $dbName = $container->getParameter('database_name');
        $dbUser = $container->getParameter('database_user');
        $dbPWD = $container->getParameter('database_password');
        $date = new \DateTime();
        $dumpDate = $date->format('Ymd-Hi');
        $dumpName = "$dumpDate-$dbName.sql";
        $dumpNameGZ = "$dumpName.gz";

        $io->title('Dumping database (' . $dbName . ') [' . $date->format('d/m/Y H:i') . ']');

        $io->section('Création du dump');

        // Calcul la durée que prend le dump
        $start = microtime(true);
        exec("mysqldump $dbName --user=$dbUser --password=$dbPWD > $dumpDir/$dumpName");

        $resultCmd = null;
        $pathDump = $dumpDir . '/' . $dumpName;
        $pathDumpGZ = $dumpDir . '/' . $dumpNameGZ;
        if (file_exists($pathDump)) {
            if (($dump = fopen($pathDump, "rb")) && ($gz = gzopen($pathDumpGZ, 'w9'))) {
                while (!feof($dump))
                    gzwrite($gz, fread($dump, 1024 * 512));
                fclose($gz);
                unlink($pathDump);
            }
            else {
                $resultCmd = "Erreur lors du fopen $pathDump ou du gzopen $pathDumpGZ.";
            }
        }
        else {
            $resultCmd = "Fichier $pathDump introuvable.";
        }
        $nbSec = microtime(true) - $start;
        $io->text(number_format($nbSec, 2, '.', '') . ' secondes');

        $dumpSuccess = file_exists($dumpDir . '/' . $dumpNameGZ);

        $mailSuccess = true;
        if ($dumpEmail) {
            try {
                $mailSuccess = $this->sendmail($dumpSuccess, $resultCmd, $dumpNameGZ);
            }
            catch (\Exception $e) {
                $mailSuccess = false;
            }
        }

		if($dumpSuccess && $mailSuccess) {
			$io->success('Database (' . $dbName . ') was successfully dumped.');
		}
		else {
			$io->error(array(
                'Dump success : ' . $dumpSuccess ? 'true' : 'false',
                'Mail success : ' . $mailSuccess ? 'true' : 'false'
            ));
		}
	}

    protected function configure()
    {
        // On set le nom de la commande
	    $this->setName('app:dump:bd');

        // On set la description
	    $this->setDescription("Dump database");
    }

    private function sendmail($dumpSuccess, $resultCmd, $dumpName)
    {
        $mailer = $this->getContainer()->get('AppBundle\Service\MailerService');

        return $mailer->sendEmail(
            MailerService::DUMP_BD,
            array(
                'date' => (new \DateTime())->format('d/m/Y H:i'),
                'dumpSuccess' => $dumpSuccess,
                'resultCmd' => $resultCmd,
                'dumpName' => $dumpName
            )
        );
    }

}
