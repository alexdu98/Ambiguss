<?php

namespace AppBundle\Listener;

use AppBundle\Event\AmbigussEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class AmbigussListener implements EventSubscriberInterface
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            AmbigussEvents::GAME_PLAYED => 'gamePlayed',
            AmbigussEvents::PHRASE_CREEE => 'phraseCreee',
            AmbigussEvents::PHRASE_AIMEE => 'phraseAimee',
            AmbigussEvents::SIGNALEMENT_VALIDE => 'signalementValide',
            AmbigussEvents::POINTS_GAGNES => 'pointsGagnes',
            AmbigussEvents::REINITIALISATION_POINTS => 'reinitialisationPoints'
        ];
    }

    public function gamePlayed(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membre = $event['membre'];

        $badgeService->check($membre, 'JOUER_PARTIE_TOTAL');
        $badgeService->check($membre, 'JOUER_PARTIE_1_JOUR');
        $badgeService->check($membre, 'JOUER_PARTIE_3_JOURS');
        $badgeService->check($membre, 'JOUER_PARTIE_7_JOURS');
    }

    public function phraseCreee(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membre = $event['membre'];

        $badgeService->check($membre, 'CREER_PHRASE_TOTAL');
        $badgeService->check($membre, 'CREER_PHRASE_1_JOUR');
        $badgeService->check($membre, 'CREER_PHRASE_3_JOURS');
        $badgeService->check($membre, 'CREER_PHRASE_7_JOURS');
    }

    public function phraseAimee(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membre = $event['membre'];

        $badgeService->check($membre, 'RECEVOIR_JAIME_TOTAL');
        $badgeService->check($membre, 'RECEVOIR_JAIME_1_PHRASE');
    }

    public function signalementValide(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membre = $event['membre'];

        $badgeService->check($membre, 'SIGNALEMENT_VALIDE_TOTAL');
    }

    public function pointsGagnes(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membre = $event['membre'];

        $badgeService->check($membre, 'CLASSEMENT_GEN');
    }

    public function reinitialisationPoints(GenericEvent $event)
    {
        $badgeService = $this->container->get('AppBundle\Service\BadgeService');

        $membres = $event['membres'];

        if ($event['type'] == 'weekly') {
            foreach ($membres as $membre) {
                $badgeService->check($membre, 'CLASSEMENT_HEBDO');
            }
        }
        else if ($event['type'] == 'monthly') {
            foreach ($membres as $membre) {
                $badgeService->check($membre, 'CLASSEMENT_MEN');
            }
        }


    }

}
