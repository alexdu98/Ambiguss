<?php

namespace App\EventSubscriber;

use App\Event\MembreEvents;
use App\Service\HistoriqueService;
use HWI\Bundle\OAuthBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangePasswordSubscriber implements EventSubscriberInterface
{

    private $historique;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historique = $historiqueService;
    }

    public static function getSubscribedEvents()
    {
        return [
            MembreEvents::CHANGE_PASSWORD_COMPLETED => 'process'
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
