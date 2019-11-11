<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Glose
 *
 * @ORM\Table(
 *     name="glose",
 *     indexes={
 *         @ORM\Index(name="ix_glose_dtcreat", columns={"date_creation"}),
 *         @ORM\Index(name="ix_glose_dtmodif", columns={"date_modification"}),
 *         @ORM\Index(name="ix_glose_modifid", columns={"modificateur_id"}),
 *         @ORM\Index(name="ix_glose_autid", columns={"auteur_id"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_glose_val", columns={"valeur"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GloseRepository")
 */
class Glose implements \JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="gloses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="Membre")
     */
    private $modificateur;

	/**
	 * @ORM\ManyToMany(targetEntity="MotAmbigu", mappedBy="gloses", cascade={"persist"})
	 */
    private $motsAmbigus;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
	    $this->signale = false;
	    $this->visible = true;
	    $this->motsAmbigus = new ArrayCollection();
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
     * @return Membre
     */
	public function getAuteur()
    {
	    return $this->auteur;
    }

    /**
     * Set auteur
     *
     * @param Membre $auteur
     *
     * @return Glose
     */
    public function setAuteur(Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get modificateur
     *
     * @return Membre
     */
	public function getModificateur()
    {
	    return $this->modificateur;
    }

    /**
     * Set modificateur
     *
     * @param Membre $modificateur
     *
     * @return Glose
     */
    public function setModificateur(Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Add motsAmbigus
     *
     * @param MotAmbigu $motsAmbigus
     *
     * @return Glose
     */
    public function addMotsAmbigus(MotAmbigu $motsAmbigus)
    {
        $this->motsAmbigus[] = $motsAmbigus;

        return $this;
    }

    /**
     * Remove motsAmbigus
     *
     * @param MotAmbigu $motsAmbigus
     */
    public function removeMotsAmbigus(MotAmbigu $motsAmbigus)
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
		$this->setValeur(mb_strtolower($this->getValeur()));

        // Trim
        $this->setValeur(trim($this->getValeur()));
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

	/**
	 * IMPLEMENTS JsonSerializable
	 */
	public function jsonSerialize()
	{
		$modificateur = !empty($this->modificateur) ? $this->modificateur->getUsername() : '';
		$dateModification = !empty($this->dateModification) ? $this->dateModification->getTimestamp() : '';

		return array(
			$this->id,
			$this->valeur,
			$this->auteur->getUsername(),
			$this->dateCreation->getTimestamp(),
			$modificateur,
			$dateModification,
			$this->signale,
			$this->visible,
		);
	}

    public function __toString()
    {
        return $this->valeur;
    }
}
