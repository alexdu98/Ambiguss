<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JugementExtension extends AbstractExtension
{

	private $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

    /**
     * Retourne le nombre de phrases signalées
     *
     * @return mixed
     */
	public function nbPhrasesSignale()
	{
		return $this->em->getRepository('AppBundle:Phrase')->countSignale();
	}

    /**
     * Retourne le nombre de gloses signalées
     *
     * @return mixed
     */
	public function nbGlosesSignale()
	{
		return $this->em->getRepository('AppBundle:Glose')->countSignale();
	}

	public function getFunctions()
	{
		return array(
			new TwigFunction('nbPhrasesSignale', array(
				$this,
				'nbPhrasesSignale',
			)),
			new TwigFunction('nbGlosesSignale', array(
				$this,
				'nbGlosesSignale',
			)),
		);
	}
}
