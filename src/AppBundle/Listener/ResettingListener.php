<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use AppBundle\Service\HistoriqueService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ResettingListener implements EventSubscriberInterface
{

    private $container;
    private $historique;
    private $flashBag;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->historique = $container->get('AppBundle\Service\HistoriqueService');
        $this->flashBag = $container->get('session')->getFlashBag();
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'sendEmailInitialize',
            FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED => 'sendEmailCompleted',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetCompleted'
        ];
    }

    /**
     * Affiche un message d'erreur si besoin
     *
     * @param GetResponseNullableUserEvent $event
     */
    public function sendEmailInitialize(GetResponseNullableUserEvent $event)
    {
        $user = $event->getUser();
        $retry_ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

        $needRedirect = false;
        if (empty($event->getRequest()->request->get('username'))) {
            $this->flashBag->add('danger', 'Vous devez renseigner un pseudo ou un email.');

            $needRedirect = true;
        }
        elseif (!$user) {
            $this->flashBag->add('danger', 'Aucun utilisateur trouvé.');

            $needRedirect = true;
        }
        elseif ($user->isPasswordRequestNonExpired($retry_ttl)) {
            $lastReq = $user->getPasswordRequestedAt()->format('H\hi');
            $nextReqTS = $user->getPasswordRequestedAt()->getTimestamp() + $retry_ttl;
            $nextReq = (new \DateTime())->setTimestamp($nextReqTS)->format('H\hi');

            $msg = 'Un email a déjà été envoyé à ' . $lastReq . ' à l\'adresse "' . $user->getEmailCanonical() . '".<br>Merci d\'attendre ' . $nextReq . '.';
            $this->flashBag->add('danger', $msg);

            $needRedirect = true;
        }

        if ($needRedirect) {
            $url = $this->container->get('router')->generate('fos_user_resetting_request');
            $response = new RedirectResponse($url);

            $event->setResponse($response);
        }
    }

    /**
     * Enregistre la demande de réinitialisation du mot de passe dans l'historique du membre
     *
     * @param GetResponseUserEvent $event
     */
    public function sendEmailCompleted(GetResponseUserEvent $event)
    {
        $user = $event->getUser();

        $this->flashBag->add('success', 'Veuillez cliquer sur le lien de réinitialisation de mot de passe envoyé par email à l\'adresse "' . $user->getEmailCanonical() . '".');

        $this->historique->save($user, "Demande de réinitialisation du mot de passe (IP : " . $event->getRequest()->server->get('REMOTE_ADDR') . ").", true);
    }

    /**
     * Enregistre la réinitialisation du mot de passe dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function resetCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $this->historique->save($user, "Réinitialisation du mot de passe (IP : " . $event->getRequest()->server->get('REMOTE_ADDR') . ").", true);

        $url = $this->container->get('router')->generate('fos_user_resetting_request');
        $response = new RedirectResponse($url);

        $event->setResponse($response);
    }

}
