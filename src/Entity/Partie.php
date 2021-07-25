<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Partie
 *
 * @ORM\Table(
 *     name="partie",
 *     indexes={
 *         @ORM\Index(name="ix_part_phraseid", columns={"phrase_id"}),
 *         @ORM\Index(name="ix_part_joueurid", columns={"joueur_id"}),
 *         @ORM\Index(name="ix_part_dtpart", columns={"date_partie"}),
 *         @ORM\Index(name="ix_part_gainjoueur", columns={"gain_joueur"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\Membre", inversedBy="parties")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $joueur;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Phrase", inversedBy="parties")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phrase;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->joue = true;
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
	 * @return Membre
	 */
	public function getJoueur()
	{
		return $this->joueur;
	}

	/**
	 * Set joueur
	 *
	 * @param Membre $joueur
	 *
	 * @return Partie
	 */
	public function setJoueur(Membre $joueur)
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

    public function __toString()
    {
        return (string) $this->joueur . ' : ' . $this->phrase;
    }

}
