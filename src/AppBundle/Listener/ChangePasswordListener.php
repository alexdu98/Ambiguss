<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangePasswordListener implements EventSubscriberInterface
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'process'
        ];
    }

    public function process(FilterUserResponseEvent $event){
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Modification du mot de passe (IP : " . $_SERVER['REMOTE_ADDR'] . ").");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }
}