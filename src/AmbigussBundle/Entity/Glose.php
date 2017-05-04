<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Glose
 *
 * @ORM\Table(name="glose", indexes={
 *     @ORM\Index(name="IDX_GLOSE_DATECREATION", columns={"date_creation"}),
 *     @ORM\Index(name="IDX_GLOSE_DATEMODIFICATION", columns={"date_modification"})
 * })
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\GloseRepository")
 */
class Glose
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre", inversedBy="gloses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
     */
    private $modificateur;

	/**
	 * @ORM\ManyToMany(targetEntity="AmbigussBundle\Entity\MotAmbigu", mappedBy="gloses", cascade={"persist"})
	 */
    private $motsAmbigus;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
	    $this->signale = 0;
	    $this->visible = 1;
	    $this->motsAmbigus = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Glose
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
     * @return Glose
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get signale
     *
     * @return bool
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
     * @return Glose
     */
    public function setSignale($signale)
    {
        $this->signale = $signale;

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
     * @return Glose
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

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
     * @return Glose
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
     * @return Glose
     */
    public function setModificateur(\UserBundle\Entity\Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Add motsAmbigus
     *
     * @param \AmbigussBundle\Entity\MotAmbigu $motsAmbigus
     *
     * @return Glose
     */
    public function addMotsAmbigus(\AmbigussBundle\Entity\MotAmbigu $motsAmbigus)
    {
        $this->motsAmbigus[] = $motsAmbigus;

        return $this;
    }

    /**
     * Remove motsAmbigus
     *
     * @param \AmbigussBundle\Entity\MotAmbigu $motsAmbigus
     */
    public function removeMotsAmbigus(\AmbigussBundle\Entity\MotAmbigu $motsAmbigus)
    {
        $this->motsAmbigus->removeElement($motsAmbigus);
    }

    /**
     * Get motsAmbigus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotsAmbigus()
    {
        return $this->motsAmbigus;
    }

	/**
	 * Normalise la glose
	 */
	public function normalize()
	{
		// Supprime les espaces multiples
		$this->setValeur(preg_replace('#\s+#', ' ', $this->getValeur()));

		// Minuscule
		$this->setValeur(strtolower($this->getValeur()));
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
	 * @return Glose
	 */
	public function setValeur($valeur)
	{
		$this->valeur = $valeur;

		return $this;
	}
}
