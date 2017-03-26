<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Phrase
 *
 * @ORM\Table(name="phrase")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\PhraseRepository")
 */
class Phrase
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
     * @ORM\Column(name="contenu", type="string", length=1024)
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
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 */
	private $modificateur;

	/**
	 * @ORM\OneToMany(targetEntity="AmbigussBundle\Entity\MotAmbiguPhrase", mappedBy="phrase", cascade={"persist"})
	 */
	private $motsAmbigusPhrase;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->signale = 0;
        $this->visible = 1;
	    $this->motsAmbigusPhrase = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Phrase
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
     * @return Phrase
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
     * @return Phrase
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
     * @return Phrase
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
     * @return Phrase
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
     * Set auteur
     *
     * @param \UserBundle\Entity\Membre $auteur
     *
     * @return Phrase
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
     * @return Phrase
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

    /**
     * Add motAmbiguPhrase
     *
     * @param \AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase
     *
     * @return Phrase
     */
    public function addMotAmbiguPhrase(\AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase[] = $motAmbiguPhrase;

        return $this;
    }

    /**
     * Remove motAmbiguPhrase
     *
     * @param \AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase
     */
    public function removeMotAmbiguPhrase(\AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase->removeElement($motAmbiguPhrase);
    }

	/**
	 * Remove motsAmbigusPhrase
	 */
	public function removeMotsAmbigusPhrase()
	{
		$this->motsAmbigusPhrase = new \Doctrine\Common\Collections\ArrayCollection();
	}

    /**
     * Get motsAmbigusPhrase
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotsAmbigusPhrase()
    {
        return $this->motsAmbigusPhrase;
    }

    /**
     * AUTRES
     */

    public function getContenuHTML(){
    	return preg_replace('#<amb id="([0-9]+)">(.*?)</amb>#', '<b class="color-red amb-border" title="Ce mot est ambigu (id : $1)">$2</b>', $this->getContenu());
    }
}
