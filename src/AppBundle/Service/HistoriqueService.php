<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManagerInterface;

class HistoriqueService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function save(Membre $membre, $message){
        $histJoueur = new \AppBundle\Entity\Historique();
        $histJoueur->setMembre($membre);
        $histJoueur->setValeur($message);

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

}
