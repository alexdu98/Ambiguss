<?php

namespace UserBundle\Listeners;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use UserBundle\Entity\Historique;

class AuthentificationListener{

	private $em;

	public function __construct(EntityManager $em){
		$this->em = $em;
	}

	public function onAuthentificationSuccess(InteractiveLoginEvent $event){
		$membre = $event->getAuthenticationToken()->getUser();
		$membre->setDateConnexion(new \DateTime());

		// On enregistre dans l'historique du joueur
		$histJoueur = new Historique();
		$histJoueur->setValeur("Connexion (IP : " . $_SERVER['REMOTE_ADDR'] . ").");
		$histJoueur->setMembre($membre);

		$this->em->persist($membre);
		$this->em->persist($histJoueur);

		$this->em->flush();
	}

}