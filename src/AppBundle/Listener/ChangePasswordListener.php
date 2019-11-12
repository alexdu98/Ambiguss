<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use AppBundle\Service\HistoriqueService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangePasswordListener implements EventSubscriberInterface
{

    private $historique;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historique = $historiqueService;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'process'
        ];
    }

    /**
     * Enregistre la modification du mot de passse dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function process(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $this->historique->save($user, "Modification du mot de passe (IP : " . $event->getRequest()->server->get('REMOTE_ADDR') . ").", true);
    }

}
