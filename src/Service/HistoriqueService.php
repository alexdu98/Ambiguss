<?php

namespace App\Service;

use App\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\RequestStack;

class HistoriqueService
{
    private $em;
    private $msg_start_modo;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->em = $entityManager;
        if (!empty($requestStack->getCurrentRequest()))
            $this->msg_start_modo = "[modo:{$requestStack->getCurrentRequest()->server->get('REMOTE_ADDR')}] ";
    }

    /**
     * Enregistre le message dans l'historique du membre
     *
     * @param UserInterface $membre
     * @param $message
     * @param $flush
     * @param $modo
     */
    public function save(UserInterface $membre, $message, $flush = false, $modo = false)
    {
        $msg_start = $modo ? $this->msg_start_modo : null;

        $histJoueur = new Historique();
        $histJoueur->setMembre($membre);
        $histJoueur->setValeur($msg_start . $message);

        $this->em->persist($histJoueur);

        if($flush){
            $this->em->flush();
        }
    }

}
