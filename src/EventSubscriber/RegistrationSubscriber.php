<?php

namespace App\EventSubscriber;

use App\Entity\Groupe;
use App\Event\MembreEvents;
use HWI\Bundle\OAuthBundle\Event\FilterUserResponseEvent;
use HWI\Bundle\OAuthBundle\Event\GetResponseUserEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationSubscriber implements EventSubscriberInterface
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
            MembreEvents::REGISTRATION_INITIALIZE => ['initialize', 100],
            MembreEvents::REGISTRATION_CONFIRM => 'confirm',
            MembreEvents::REGISTRATION_CONFIRMED => 'confirmed',
            MembreEvents::REGISTRATION_SUCCESS => ['success', -100],
            MembreEvents::REGISTRATION_COMPLETED => ['completed', -100],
            MembreEvents::REGISTRATION_FAILURE => 'failure'
        ];
    }

    /**
     * Vérifie le captcha
     *
     * @param FormEvent $event
     */
    public function initialize(GetResponseUserEvent $event)
    {
        if ($event->getRequest()->isMethod('POST')) {
            $recaptchaService = $this->container->get('App\Service\RecaptchaService');

            $captcha = $event->getRequest()->request->get('g-recaptcha-response');
            $ip = $event->getRequest()->server->get('REMOTE_ADDR');

            $recaptcha = $recaptchaService->check($captcha, $ip);
            if (empty($recaptcha['success']) || !$recaptcha['success']) {
                $message = implode('<br>', $recaptcha['error-codes']);
                $this->container->get('session')->getFlashBag()->add('danger', $message);

                $logger = $this->container->get('logger');
                $logInfos = array(
                    'msg' => $message,
                    'email' => 'non connecté',
                    'ip' => $event->getRequest()->server->get('REMOTE_ADDR'),
                );
                $logger->critical(json_encode($logInfos));

                $form = $this->container->get('fos_user.registration.form.factory')->createForm();
                $form->setData($event->getUser());
                $form->handleRequest($event->getRequest());

                $this->container->get('event_dispatcher')->dispatch(MembreEvents::REGISTRATION_FAILURE, new FormEvent($form, $event->getRequest()));

                $url = $this->container->get('router')->generate('fos_user_registration_register');
                $response = new RedirectResponse($url);

                $event->setResponse($response);

                $event->stopPropagation();
            }
        }
    }

    /**
     * Modification de l'URL de redirection
     *
     * @param GetResponseUserEvent $event
     */
    public function confirm(GetResponseUserEvent $event)
    {
        $url = $this->container->get('router')->generate('fos_user_security_login');
        $response = new RedirectResponse($url);

        $event->setResponse($response);
    }

    /**
     * Enregistre la confirmation d'inscription dans l'historique du membre et ajout du message flash
     *
     * @param FilterUserResponseEvent $event
     */
    public function confirmed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $historiqueService = $this->container->get('App\Service\HistoriqueService');
        $historiqueService->save($user, "Confirmation d'inscription.", true);

        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->clear();
        $flashBag->add('success', 'Inscription confirmée, vous pouvez vous connecter.');
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

        $url = $this->container->get('router')->generate('fos_user_security_login');
        $response = new RedirectResponse($url);

        $event->setResponse($response);
    }

    /**
     * Enregistre l'inscription via le site dans l'historique du membre
     *
     * @param FilterUserResponseEvent $event
     */
    public function completed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $historiqueService = $this->container->get('App\Service\HistoriqueService');
        $historiqueService->save($user, "Inscription via Ambiguss.", true);

        $email = $event->getRequest()->getSession()->get('fos_user_send_confirmation_email/email');

        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->clear();
        $flashBag->add('info', 'Inscription réussie, veuillez cliquer sur le lien de confirmation envoyé par email à l\'adresse "' . $email . '".');
    }

    /**
     * @param FormEvent $event
     */
    public function failure(FormEvent $event)
    {
        $flashBag = $this->container->get('session')->getFlashBag();
        $logger = $this->container->get('logger');

        $msg = 'Inscription échouée, veuillez réessayer ou contacter un administrateur si cela persiste.';
        $flashBag->add('danger', $msg);

        $logInfos = array(
            'msg' => $msg,
            'email' => $event->getRequest()->getSession()->get('fos_user_send_confirmation_email/email'),
            'ip' => $event->getRequest()->server->get('REMOTE_ADDR'),
        );
        $logger->critical(json_encode($logInfos));
    }

}
