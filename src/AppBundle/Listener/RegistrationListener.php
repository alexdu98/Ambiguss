<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Groupe;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{

    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
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

        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
        $historiqueService->save($user, "Confirmation d'inscription.", true);
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

        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
        $historiqueService->save($user, "Inscription via Ambiguss.", true);
    }

}
