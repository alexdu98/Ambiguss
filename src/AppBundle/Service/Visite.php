<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class Visite {

	private $em;
	private $timeBetweenTwoVisites;

	public function __construct(EntityManagerInterface $em, $timeBetweenTwoVisites){
		$this->em = $em;
		$this->timeBetweenTwoVisites = $timeBetweenTwoVisites;
	}

	/**
	 * Ajoute une visite
	 */
	public function checkAndAdd(){
		if (empty($_COOKIE['visite']))
		{
			$time = $this->timeBetweenTwoVisites;
			if(($next = $this->em->getRepository('AppBundle:Visite')->checkVisite($time)) !== true){
				$time = $next;
			}
			setcookie('visite', 'visited', time() + $time);
		}
	}

}