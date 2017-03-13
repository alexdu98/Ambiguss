<?php

namespace AppBundle\Services;

use AppBundle\Entity;
use Doctrine\ORM\EntityManager;

class Visite extends \Twig_Extension{

	private $em;

	public function __construct(EntityManager $em){
		$this->em = $em;
	}

	/**
	 * Ajoute une visite si
	 */
	public function checkAndAdd(){
		if (empty($_COOKIE['visite']))
		{
			setcookie('visite', 'visited', time() + (3600 * 24));
			$this->em->getRepository('AppBundle:Visite')->checkVisite();
		}
	}

	/**
	 * IMPLEMENTS Twig_Extension
	 */

	public function getFunctions(){
		return array(
			new \Twig_SimpleFunction('checkAndAdd', array($this, 'checkAndAdd'))
		);
	}

	public function getName(){
		return 'Visite';
	}

}