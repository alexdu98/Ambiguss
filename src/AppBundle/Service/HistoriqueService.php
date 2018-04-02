<?php

namespace AppBundle\Service;

use AppBundle\Entity\Historique;
use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManagerInterface;

class HistoriqueService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Enregistre le message dans l'historique du membre
     *
     * @param Membre $membre
     * @param $message
     */
    public function save(Membre $membre, $message){
        $histJoueur = new Historique();
        $histJoueur->setMembre($membre);
        $histJoueur->setValeur($message);

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

}
