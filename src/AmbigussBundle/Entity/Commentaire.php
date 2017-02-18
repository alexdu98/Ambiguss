<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\CommentaireRepository")
 */
class Commentaire
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
     * @ORM\Column(name="contenu", type="string", length=512)
     */
    private $contenu;

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
     * @ORM\ManyToOne(targetEntity="Commentaire")
     */
    private $commentaireParent;

	/**
	 * @ORM\ManyToOne(targetEntity="Membre")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="Membre")
	 */
	private $modificateur;

	/**
	 * @ORM\ManyToOne(targetEntity="Jugement")
	 */
	private $jugement;

	/**
	 * @ORM\ManyToOne(targetEntity="Phrase")
	 */
	private $phrase;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
	    $this->signale = 0;
	    $this->visible = 1;
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
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Commentaire
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Commentaire
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
     * @return Commentaire
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
     * Set signale
     *
     * @param boolean $signale
     *
     * @return Commentaire
     */
    public function setSignale($signale)
    {
        $this->signale = $signale;

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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Commentaire
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

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
     * Set commentaireParent
     *
     * @param \AmbigussBundle\Entity\Commentaire $commentaireParent
     *
     * @return Commentaire
     */
    public function setCommentaireParent(\AmbigussBundle\Entity\Commentaire $commentaireParent = null)
    {
        $this->commentaireParent = $commentaireParent;

        return $this;
    }

    /**
     * Get commentaireParent
     *
     * @return \AmbigussBundle\Entity\Commentaire
     */
    public function getCommentaireParent()
    {
        return $this->commentaireParent;
    }

    /**
     * Set auteur
     *
     * @param \AmbigussBundle\Entity\Membre $auteur
     *
     * @return Commentaire
     */
    public function setAuteur(\AmbigussBundle\Entity\Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set modificateur
     *
     * @param \AmbigussBundle\Entity\Membre $modificateur
     *
     * @return Commentaire
     */
    public function setModificateur(\AmbigussBundle\Entity\Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Get modificateur
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getModificateur()
    {
        return $this->modificateur;
    }

    /**
     * Set jugement
     *
     * @param \AmbigussBundle\Entity\Jugement $jugement
     *
     * @return Commentaire
     */
    public function setJugement(\AmbigussBundle\Entity\Jugement $jugement = null)
    {
        $this->jugement = $jugement;

        return $this;
    }

    /**
     * Get jugement
     *
     * @return \AmbigussBundle\Entity\Jugement
     */
    public function getJugement()
    {
        return $this->jugement;
    }

    /**
     * Set phrase
     *
     * @param \AmbigussBundle\Entity\Phrase $phrase
     *
     * @return Commentaire
     */
    public function setPhrase(\AmbigussBundle\Entity\Phrase $phrase = null)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get phrase
     *
     * @return \AmbigussBundle\Entity\Phrase
     */
    public function getPhrase()
    {
        return $this->phrase;
    }
}
