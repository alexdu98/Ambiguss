<?php

namespace AppBundle\Listener;

use AppBundle\Service\HistoriqueService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginListener implements EventSubscriberInterface
{

    private $historique;

    public function __construct(HistoriqueService $historiqueService)
    {
        $this->historique = $historiqueService;
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'process'
        ];
    }

    /**
     * Enregistre la connexion dans l'historique du membre
     *
     * @param InteractiveLoginEvent $event
     */
    public function process(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        $this->historique->save($user, "Connexion (IP : " . $_SERVER['REMOTE_ADDR'] . ").", true);
    }

}
