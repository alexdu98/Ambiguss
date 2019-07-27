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
     * Retourne le nombre de membres signalés
     *
     * @return mixed
     */
    public function nbMembresSignales()
    {
        return $this->em->getRepository('AppBundle:Membre')->countSignale();
    }

    /**
     * Retourne le nombre de phrases signalées
     *
     * @return mixed
     */
	public function nbPhrasesSignalees()
	{
		return $this->em->getRepository('AppBundle:Phrase')->countSignale();
	}

    /**
     * Retourne le nombre de gloses signalées
     *
     * @return mixed
     */
	public function nbGlosesSignalees()
	{
		return $this->em->getRepository('AppBundle:Glose')->countSignale();
	}

	public function getFunctions()
	{
		return array(
            new TwigFunction('nbMembresSignales', array(
                $this,
                'nbMembresSignales',
            )),
			new TwigFunction('nbPhrasesSignalees', array(
				$this,
				'nbPhrasesSignalees',
			)),
			new TwigFunction('nbGlosesSignalees', array(
				$this,
				'nbGlosesSignalees',
			)),
		);
	}
}
