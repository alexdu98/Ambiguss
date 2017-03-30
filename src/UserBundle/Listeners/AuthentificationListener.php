<?php

namespace UserBundle\Listeners;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthentificationListener{

	private $em;

	public function __construct(EntityManager $em){
		$this->em = $em;
	}

	public function onAuthentificationSuccess(InteractiveLoginEvent  $event){
		$membre = $event->getAuthenticationToken()->getUser();
		$membre->setDateConnexion(new \DateTime());

		$this->em->persist($membre);
		$this->em->flush();
	}

}