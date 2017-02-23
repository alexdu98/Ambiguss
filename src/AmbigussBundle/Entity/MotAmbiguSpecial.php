<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotAmbiguSpecial
 *
 * @ORM\Table(name="mot_ambigu_special")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\MotAmbiguSpecialRepository")
 */
class MotAmbiguSpecial
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
     * @var string
     *
     * @ORM\Column(name="valeur", type="string", length=32, unique=true)
     */
    private $valeur;

    /**
     * @var bool
     *
     * @ORM\Column(name="ambigu", type="boolean")
     */
    private $ambigu;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_creation", type="datetime")
	 */
	private $dateCreation;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_modification", type="datetime", nullable=true)
	 */
	private $dateModification;

    /**
     * @ORM\ManyToOne(targetEntity="MotAmbigu")
     */
    private $motAmbigu;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 */
	private $modificateur;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dateCreation = new \DateTime();
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
     * Set valeur
     *
     * @param string $valeur
     *
     * @return MotAmbiguSpecial
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set ambigu
     *
     * @param boolean $ambigu
     *
     * @return MotAmbiguSpecial
     */
    public function setAmbigu($ambigu)
    {
        $this->ambigu = $ambigu;

        return $this;
    }

    /**
     * Get ambigu
     *
     * @return bool
     */
    public function getAmbigu()
    {
        return $this->ambigu;
    }

	/**
	 * Set dateCreation
	 *
	 * @param \DateTime $dateCreation
	 *
	 * @return MotAmbigu
	 */
	public function setDateCreation($dateCreation)
	{
		$this->dateCreation = $dateCreation;

		return $this;
	}

	/**
	 * Get dateCreation
	 *
	 * @return \DateTime
	 */
	public function getDateCreation()
	{
		return $this->dateCreation;
	}

	/**
	 * Set dateModification
	 *
	 * @param \DateTime $dateModification
	 *
	 * @return MotAmbigu
	 */
	public function setDateModification($dateModification)
	{
		$this->dateModification = $dateModification;

		return $this;
	}

	/**
	 * Get dateModification
	 *
	 * @return \DateTime
	 */
	public function getDateModification()
	{
		return $this->dateModification;
	}

    /**
     * Set motAmbigu
     *
     * @param \AmbigussBundle\Entity\MotAmbigu $motAmbigu
     *
     * @return MotAmbiguSpecial
     */
    public function setMotAmbigu(\AmbigussBundle\Entity\MotAmbigu $motAmbigu = null)
    {
        $this->motAmbigu = $motAmbigu;

        return $this;
    }

    /**
     * Get motAmbigu
     *
     * @return \AmbigussBundle\Entity\MotAmbigu
     */
    public function getMotAmbigu()
    {
        return $this->motAmbigu;
    }

    /**
     * Set auteur
     *
     * @param \UserBundle\Entity\Membre $auteur
     *
     * @return MotAmbiguSpecial
     */
    public function setAuteur(\UserBundle\Entity\Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \UserBundle\Entity\Membre
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set modificateur
     *
     * @param \UserBundle\Entity\Membre $modificateur
     *
     * @return MotAmbiguSpecial
     */
    public function setModificateur(\UserBundle\Entity\Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Get modificateur
     *
     * @return \UserBundle\Entity\Membre
     */
    public function getModificateur()
    {
        return $this->modificateur;
    }
}
