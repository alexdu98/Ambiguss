<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use AppBundle\Service\HistoriqueService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResettingListener implements EventSubscriberInterface
{

    private $historique;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historique = $historiqueService;
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

        $this->historique->save($user, "Demande de réinitialisation du mot de passe (IP : " . $event->getRequest()->server->get('REMOTE_ADDR') . ").", true);
    }

    /**
     * Enregistre la réinitialisation du mot de passe dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function resettingProcess(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $this->historique->save($user, "Réinitialisation du mot de passe (IP : " . $event->getRequest()->server->get('REMOTE_ADDR') . ").", true);
    }

}
