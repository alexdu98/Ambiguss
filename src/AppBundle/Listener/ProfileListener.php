<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Historique;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfileListener implements EventSubscriberInterface
{

    private $em;
    private $router;
    private $oldUsername;
    private $oldEmail;
    private $oldSexe;
    private $oldDateNaissance;
    private $oldNewsletter;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $router)
    {
        $this->em = $entityManager;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::PROFILE_EDIT_INITIALIZE => 'initialize',
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'process'
        ];
    }

    /**
     * Enregistre les précédentes valeurs du profil du membre
     *
     * @param GetResponseUserEvent $event
     */
    public function initialize(GetResponseUserEvent $event){
        $this->oldUsername = $event->getUser()->getUsername();
        $this->oldEmail = $event->getUser()->getEmail();
        $this->oldSexe = $event->getUser()->getSexe();
        $this->oldDateNaissance = $event->getUser()->getDateNaissance();
        $this->oldNewsletter = $event->getUser()->getNewsletter();
    }

    /**
     * Enregistre les modifications du profil du membre dans son historique
     *
     * @param FilterUserResponseEvent $event
     */
    public function process(FilterUserResponseEvent $event){
        $newUser = $event->getUser();

        $infos = array();
        if($this->oldUsername != $newUser->getUsername()){
            $infos[] = "pseudo : {$this->oldUsername} => {$newUser->getUsername()}";
            $newUser->setRenamable(false);
        }
        if($this->oldEmail != $newUser->getEmail()){
            $infos[] = "email : {$this->oldEmail} => {$newUser->getEmail()}";
        }
        if($this->oldSexe != $newUser->getSexe()){
            $infos[] = "genre : {$this->oldSexe} => {$newUser->getSexe()}";
        }
        if($this->oldDateNaissance != $newUser->getDateNaissance()){
            $infos[] = "date de naissance : {$this->oldDateNaissance->format('d/m/Y')} => {$newUser->getDateNaissance()->format('d/m/Y')}";
        }
        if($this->oldNewsletter != $newUser->getNewsletter()){
            $oldNewsletter = $this->oldNewsletter === false ? 'non abonné' : 'abonné';
            $newNewsletter = $newUser->getNewsletter() === false ? 'non abonné' : 'abonné';
            $infos[] = "newsletter : {$oldNewsletter} => {$newNewsletter}";
        }

        $histJoueur = new Historique();
        $histJoueur->setMembre($newUser);
        $histJoueur->setValeur("Modification des informations (IP : {$_SERVER['REMOTE_ADDR']} / " . implode(', ', $infos) . ").");

        $this->em->persist($histJoueur);
        $this->em->persist($newUser);
        $this->em->flush();
    }

}
