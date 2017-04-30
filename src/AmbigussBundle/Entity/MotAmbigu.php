<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotAmbigu
 *
 * @ORM\Table(name="mot_ambigu")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\MotAmbiguRepository")
 */
class MotAmbigu
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
     * @ORM\Column(name="valeur", type="string", length=32, unique=true, options={"collation":"utf8_bin"})
     */
    private $valeur;

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
	 * @var bool
	 *
	 * @ORM\Column(name="signale", type="boolean")
	 */
	private $signale;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="visible", type="boolean")
     */
	private $visible;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre", inversedBy="motsAmbigus")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 */
	private $modificateur;

    /**
     * @ORM\ManyToMany(targetEntity="Glose", inversedBy="motsAmbigus", cascade={"persist"})
     * @ORM\JoinTable(name="mot_ambigu_glose")
     */
    private $gloses = array();


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
	    $this->signale = 0;
	    $this->visible = 1;
	    $this->gloses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get dateCreation
     *
     * @return \DateTime
     */
	public function getDateCreation()
    {
	    return $this->dateCreation;
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
     * Get dateModification
     *
     * @return \DateTime
     */
	public function getDateModification()
    {
	    return $this->dateModification;
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
     * Get visible
     *
     * @return bool
     */
	public function getVisible()
    {
	    return $this->visible;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return MotAmbigu
     */
	public function setVisible($visible)
    {
	    $this->visible = $visible;

        return $this;
    }

    /**
     * Add glose
     *
     * @param \AmbigussBundle\Entity\Glose $glose
     *
     * @return MotAmbigu
     */
    public function addGlose(\AmbigussBundle\Entity\Glose $glose)
    {
        $this->gloses[] = $glose;

        return $this;
    }

    /**
     * Remove glose
     *
     * @param \AmbigussBundle\Entity\Glose $glose
     */
    public function removeGlose(\AmbigussBundle\Entity\Glose $glose)
    {
        $this->gloses->removeElement($glose);
    }

    /**
     * Get gloses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGloses()
    {
        return $this->gloses;
    }

	/**
	 * Add glose if not exist
	 *
	 * @param \AmbigussBundle\Entity\Glose $glose
	 *
	 * @return MotAmbigu
	 */
	public function addGloseIfNotExist(\AmbigussBundle\Entity\Glose $glose)
	{
		$this->gloses[ $glose->getId() ] = $glose;

		return $this;
	}

	/**
	 * Get signale
	 *
	 * @return boolean
	 */
	public function getSignale()
	{
		return $this->signale;
	}

	/**
	 * Set signale
	 *
	 * @param boolean $signale
	 *
	 * @return MotAmbigu
	 */
	public function setSignale($signale)
	{
		$this->signale = $signale;

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
	 * Set auteur
	 *
	 * @param \UserBundle\Entity\Membre $auteur
	 *
	 * @return MotAmbigu
	 */
	public function setAuteur(\UserBundle\Entity\Membre $auteur)
	{
		$this->auteur = $auteur;

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

	/**
	 * Set modificateur
	 *
	 * @param \UserBundle\Entity\Membre $modificateur
	 *
	 * @return MotAmbigu
	 */
	public function setModificateur(\UserBundle\Entity\Membre $modificateur = null)
	{
		$this->modificateur = $modificateur;

		return $this;
	}

	/**
	 * Normalise le mot ambigu
	 */
	public function normalize()
	{
		// Supprime les espaces multiples
		$this->setValeur(preg_replace('#\s+#', ' ', $this->getValeur()));
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
	 * Set valeur
	 *
	 * @param string $valeur
	 *
	 * @return MotAmbigu
	 */
	public function setValeur($valeur)
	{
		$this->valeur = $valeur;

		return $this;
	}
}
