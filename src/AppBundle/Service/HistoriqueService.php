<?php

namespace AppBundle\Service;

use AppBundle\Entity\Historique;
use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;

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
     * @param UserInterface $membre
     * @param $message
     */
    public function save(UserInterface $membre, $message, $flush = false){
        $histJoueur = new Historique();
        $histJoueur->setMembre($membre);
        $histJoueur->setValeur($message);

        $this->em->persist($histJoueur);

        if($flush){
            $this->em->flush();
        }
    }

}
