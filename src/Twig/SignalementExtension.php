<?php

namespace App\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SignalementExtension extends AbstractExtension
{

	private $em;
	private $nbMembresSignales;
	private $nbPhrasesSignalees;
	private $nbGlosesSignalees;
	private $nbSignalementsEnCours;

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
        return $this->nbMembresSignales ?? ($this->nbMembresSignales = $this->em->getRepository('App:Membre')->countSignale());
    }

    /**
     * Retourne le nombre de phrases signalées
     *
     * @return mixed
     */
	public function nbPhrasesSignalees()
	{
		return $this->nbPhrasesSignalees ?? ($this->nbPhrasesSignalees = $this->em->getRepository('App:Phrase')->countSignale());
	}

    /**
     * Retourne le nombre de gloses signalées
     *
     * @return mixed
     */
	public function nbGlosesSignalees()
	{
		return $this->nbGlosesSignalees ?? ($this->nbGlosesSignalees = $this->em->getRepository('App:Glose')->countSignale());
	}

    /**
     * Retourne le nombre de signalements en cours
     *
     * @return mixed
     */
    public function nbSignalementsEnCours()
    {
        return $this->nbSignalementsEnCours ?? ($this->nbSignalementsEnCours = $this->em->getRepository('App:Signalement')->countEnCours());
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
            new TwigFunction('nbSignalementsEnCours', array(
                $this,
                'nbSignalementsEnCours',
            ))
		);
	}
}
