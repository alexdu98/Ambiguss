<?php

namespace JudgmentBundle\Services;

use Doctrine\ORM\EntityManager;

class Jugement extends \Twig_Extension
{

	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function nbPhrasesSignale()
	{
		return $this->em->getRepository('AmbigussBundle:Phrase')->countSignale();
	}

	public function nbGlosesSignale()
	{
		return $this->em->getRepository('AmbigussBundle:Glose')->countSignale();
	}

	/**
	 * IMPLEMENTS Twig_Extension
	 */

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('nbPhrasesSignale', array(
				$this,
				'nbPhrasesSignale',
			)),
			new \Twig_SimpleFunction('nbGlosesSignale', array(
				$this,
				'nbGlosesSignale',
			)),
		);
	}

	public function getName()
	{
		return 'Signale';
	}
}