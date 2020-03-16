<?php

namespace AppBundle\Service;

use AppBundle\Entity\MembreBadge;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NotifyService
{
    private $container;
    private $flashBag;
    private $router;
    private $historique;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->flashBag = $container->get('session')->getFlashBag();
        $this->router = $container->get('router');
        $this->historique = $container->get('AppBundle\Service\HistoriqueService');
    }

    public function addWinBadge(MembreBadge $membreBadge)
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if ($token && $token->getUser()->getId() == $membreBadge->getMembre()->getId()) {
            $this->flashBag->add('notifies', array(
                'type' => 'success',
                'icon' => 'fa fa-star',
                'title' => 'Vous avez gagné un nouveau badge !',
                'message' => '« ' . $membreBadge->getBadge()->getDescription() . ' » <br><small>+' . $membreBadge->getBadge()->getPoints() . ' crédits/points</small>',
                'url' => $this->router->generate('fos_user_profile_show') . '#badges'
            ));
        }
    }
}
