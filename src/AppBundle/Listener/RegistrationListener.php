<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Groupe;
use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    /**
     * Enregistre la confirmation d'inscription dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function confirmed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Confirmation d'inscription.");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

    /**
     * Ajoute le nouveau membre au groupe des membres
     *
     * @param FormEvent $event
     */
    public function success(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        $user->addGroup($this->em->getRepository(Groupe::class)->findOneBy(['name' => 'Membre']));
    }

    /**
     * Enregistre l'inscription via le site dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function completed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $histJoueur = new Historique();
        $histJoueur->setMembre($user);
        $histJoueur->setValeur("Inscription via Ambiguss.");

        $this->em->persist($histJoueur);
        $this->em->flush();
    }

}
