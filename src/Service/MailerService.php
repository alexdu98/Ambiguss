<?php

namespace App\Service;



use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerService implements MailerInterface
{
    const USER_CONFIRM = 0;
    const USER_RESET = 1;
    const CONTACT = 2;
    const DUMP_BD = 3;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Envoie le mail de confirmation d'inscription au membre
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        return $this->sendEmail(
            self::USER_CONFIRM,
            array(
                'user' => $user,
                'pseudoReceveur' => $user->getUsername()
            )
        );
    }

    /**
     * Envoie le mail de réinitialisation de mot de passe au membre
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        return $this->sendEmail(
            self::USER_RESET,
            array(
                'user' => $user,
                'pseudoReceveur' => $user->getUsername()
            )
        );
    }

    public function sendEmail($type, $params = null)
    {
        $from = $this->container->getParameter('mailer_from');
        $subject = $recipient = $template = $attach = null;

        switch ($type) {
            case self::USER_CONFIRM:
                $subject = 'Inscription';
                $recipient = $params['user']->getEmail();
                $template = 'App:Mail:inscription.html.twig';

                break;

            case self::USER_RESET:
                $subject = 'Réinitialisation de mot de passe';
                $recipient = $params['user']->getEmail();
                $template = 'App:Mail:reinitialisation.html.twig';

                break;

            case self::CONTACT:
                $subject = 'Contact';
                $recipient = $this->container->getParameter('contact_email');
                $template = 'App:Mail:contact.html.twig';

                break;

            case self::DUMP_BD:
                $subject = 'Dump du ' . $params['date'] . ' : ' . ($params['dumpSuccess'] ? 'OK' : 'KO');
                $recipient = $this->container->getParameter('dump_email');
                $template = 'App:Mail:dump.html.twig';
                $params['dump_dir'] = $this->container->getParameter('dump_dir');
                $params['dumpAbsPath'] = $params['dump_dir'] . '/' . $params['dumpName'];

                if ($params['dumpSuccess'])
                    $attach = $params['dumpAbsPath'];

                break;
        }

        $params = array_merge(
            array(
                'subject' => $subject,
                'recipient' => $recipient,
            ),
            $params
        );
        $body = $this->container->get('templating')->render($template, $params);

        return $this->sendEmailMessage($subject, $from, $recipient, $body, $attach);
    }

    /**
     * Envoie un mail
     */
    private function sendEmailMessage(string $subject, string $from, string $recipient, string $body, ?string $attach)
    {
        $message = (new \Swift_Message())
            ->setSubject('[Ambiguss] ' . $subject)
            ->setFrom($from)
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        if(!empty($attach))
            $message->attach(\Swift_Attachment::fromPath($attach));

        return $this->container->get('mailer')->send($message);
    }

}
