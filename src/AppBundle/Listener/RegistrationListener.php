<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class RegistrationListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_CONFIRMED => 'confirmed',
            FOSUserEvents::REGISTRATION_SUCCESS => 'success',
            FOSUserEvents::REGISTRATION_COMPLETED => 'completed'
        ];
    }

    public function confirmed(FilterUserResponseEvent $event){
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Confirmation d'inscription.");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

    public function success(FormEvent $event){
        $user = $event->getForm()->getData();

        // Affecte un niveau au nouveau membre
        $repository = $this->em->getRepository('AppBundle:Niveau');
        $grp = $repository->findOneByTitre('Facile');
        $user->setNiveau($grp);
    }

    public function completed(FilterUserResponseEvent $event){
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Inscription via Ambiguss.");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }
}