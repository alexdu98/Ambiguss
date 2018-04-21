<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResettingListener implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED => 'resettingDemand',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'resettingProcess'
        ];
    }

    /**
     * Enregistre la demande de réinitialisation du mot de passe dans l'historique du membre
     *
     * @param GetResponseUserEvent $event
     */
    public function resettingDemand(GetResponseUserEvent $event)
    {
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Demande de réinitialisation du mot de passe (IP : " . $_SERVER['REMOTE_ADDR'] . ").");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

    /**
     * Enregistre la réinitialisation du mot de passe dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function resettingProcess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Réinitialisation du mot de passe (IP : " . $_SERVER['REMOTE_ADDR'] . ").");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

}
