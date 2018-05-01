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
     * @return int
     * @throws \Twig\Error\Error
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $subject = 'Inscription';
        $body = $this->templating->render('AppBundle:Mail:inscription.html.twig', array(
            'recipient' => $user,
            'subject' => $subject,
        ));

        return $this->sendEmailMessage($subject, $this->from, $user->getEmail(), $body);
    }

    /**
     * Envoie le mail de réinitialisation de mot de passe au membre
     *
     * @param UserInterface $user
     * @return int
     * @throws \Twig\Error\Error
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $subject = 'Réinitialisation de mot de passe';
        $body = $this->templating->render('AppBundle:Mail:/reinitialisation.html.twig', array(
            'recipient' => $user,
            'subject' => $subject,
        ));

        return $this->sendEmailMessage($subject, $this->from, $user->getEmail(), $body);
    }

    /**
     * Envoie le mail de contact
     *
     * @param string $recipient
     * @param array $params
     * @return int
     * @throws \Twig\Error\Error
     */
    public function sendContactEmailMessage(string $recipient, array $params)
    {
        $subject = 'Contact';
        $body = $this->templating->render('AppBundle:Mail:contact.html.twig', array_merge(
            array(
                'recipient' => $recipient,
                'subject' => $subject,
            ),
            $params
        ));

        return $this->sendEmailMessage($subject, $this->from, $recipient, $body);
    }

    /**
     * Envoie un mail
     *
     * @param string $subject
     * @param string $from
     * @param string $recipient
     * @param string $body
     * @return int Le nombre de destinataire. 0 si erreur
     */
    protected function sendEmailMessage(string $subject, string $from, string $recipient, string $body)
    {
        $message = (new \Swift_Message())
            ->setSubject('[Ambiguss] ' . $subject)
            ->setFrom($from)
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        return $this->mailer->send($message);
    }

}
