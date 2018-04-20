<?php

namespace AppBundle\Service;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerService implements MailerInterface
{
    private $mailer;
    private $templating;
    private $from;

    public function __construct(ContainerInterface $container)
    {
        $this->mailer = $container->get('mailer');
        $this->templating = $container->get('templating');
        $this->from = $container->getParameter('emailFrom');
    }

    /**
     * Envoie le mail de confirmation d'inscription au membre
     *
     * @param UserInterface $user
     * @throws \Twig\Error\Error
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $subject = 'Inscription';
        $body = $this->templating->render('AppBundle:Mail:inscription.html.twig', array(
            'recipient' => $user,
            'subject' => $subject,
        ));

        $this->sendEmailMessage($subject, $user, $body);
    }

    /**
     * Envoie le mail de réinitialisation de mot de passe au membre
     *
     * @param UserInterface $user
     * @throws \Twig\Error\Error
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $subject = 'Réinitialisation de mot de passe';
        $body = $this->templating->render('AppBundle:Mail:/reinitialisation.html.twig', array(
            'recipient' => $user,
            'subject' => $subject,
        ));

        $this->sendEmailMessage($subject, $user, $body);
    }


    /**
     * Envoie un mail à un membre
     *
     * @param $subject
     * @param UserInterface $recipient
     * @param $body
     */
    protected function sendEmailMessage($subject, UserInterface $recipient, $body)
    {
        $message = (new \Swift_Message())
            ->setSubject('[Ambiguss] ' . $subject)
            ->setFrom($this->from)
            ->setTo($recipient->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}