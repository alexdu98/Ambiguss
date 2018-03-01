<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partie
 *
 * @ORM\Table(name="partie", indexes={
 *     @ORM\Index(name="IDX_PARTIE_DATEPARTIE", columns={"date_partie"}),
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartieRepository")
 */
class Partie
{

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="gain_joueur", type="integer")
	 */
	private $gainJoueur;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="joue", type="boolean")
	 */
	private $joue;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_partie", type="datetime")
	 */
	private $datePartie;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $joueur;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Phrase", inversedBy="parties")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	private $phrase;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->joue = 1;
		$this->gainJoueur = 0;
		$this->datePartie = new \DateTime();
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get gainJoueur
	 *
	 * @return int
	 */
	public function getGainJoueur()
	{
		return $this->gainJoueur;
	}

	/**
	 * Set gainJoueur
	 *
	 * @param integer $gainJoueur
	 *
	 * @return Partie
	 */
	public function setGainJoueur($gainJoueur)
	{
		$this->gainJoueur = $gainJoueur;

		return $this;
	}

	/**
	 * Get joue
	 *
	 * @return bool
	 */
	public function getJoue()
	{
		return $this->joue;
	}

	/**
	 * Set joue
	 *
	 * @param boolean $joue
	 *
	 * @return Partie
	 */
	public function setJoue($joue)
	{
		$this->joue = $joue;

		return $this;
	}

	/**
	 * Get joueur
	 *
	 * @return \UserBundle\Entity\Membre
	 */
	public function getJoueur()
	{
		return $this->joueur;
	}

	/**
	 * Set joueur
	 *
	 * @param \UserBundle\Entity\Membre $joueur
	 *
	 * @return Partie
	 */
	public function setJoueur(\UserBundle\Entity\Membre $joueur)
	{
		$this->joueur = $joueur;

		return $this;
	}

	/**
	 * Get phrase
	 *
	 * @return Phrase
	 */
	public function getPhrase()
	{
		return $this->phrase;
	}

	/**
	 * Set phrase
	 *
	 * @param Phrase $phrase
	 *
	 * @return Partie
	 */
	public function setPhrase(Phrase $phrase)
	{
		$this->phrase = $phrase;

		return $this;
	}

	/**
	 * Get datePartie
	 *
	 * @return \DateTime
	 */
	public function getDatePartie()
	{
		return $this->datePartie;
	}

	/**
	 * Set datePartie
	 *
	 * @param \DateTime $datePartie
	 *
	 * @return Partie
	 */
	public function setDatePartie($datePartie)
	{
		$this->datePartie = $datePartie;

		return $this;
	}
}
