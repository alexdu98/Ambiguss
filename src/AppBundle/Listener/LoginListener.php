<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
           SecurityEvents::INTERACTIVE_LOGIN => 'process'
        ];
    }

    public function process(InteractiveLoginEvent $event){
        $user = $event->getAuthenticationToken()->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Connexion (IP : " . $_SERVER['REMOTE_ADDR'] . ").");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }
}