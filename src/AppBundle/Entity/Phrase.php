<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Phrase
 *
 * @ORM\Table(
 *     name="phrase",
 *     indexes={
 *          @ORM\Index(name="IDX_PHRASE_DATECREATION", columns={"date_creation"}),
 *          @ORM\Index(name="IDX_PHRASE_DATEMODIFICATION", columns={"date_modification"}),
 *          @ORM\Index(name="IDX_PHRASE_GAINCREATEUR", columns={"gain_createur"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhraseRepository")
 */
class Phrase implements \JsonSerializable
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
	 * @var string
	 *
	 * @ORM\Column(name="contenu_pur", type="string", length=255, unique=true)
	 */
	private $contenuPur;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="gain_createur", type="integer")
	 */
	private $gainCreateur;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre", inversedBy="phrases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
	 */
	private $modificateur;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\MotAmbiguPhrase", mappedBy="phrase", cascade={"persist"})
     * @ORM\OrderBy({"ordre" = "ASC"})
	 */
	private $motsAmbigusPhrase;

	/**
	 * @ORM\OneToMany(targetEntity="JAime", mappedBy="phrase", cascade={"persist"})
	 */
	private $jAime;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Partie", mappedBy="phrase")
	 */
	private $parties;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->signale = false;
        $this->visible = true;
	    $this->gainCreateur = 0;
	    $this->motsAmbigusPhrase = new ArrayCollection();
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
     * @return Phrase
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
     * @return Phrase
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

	/**
	 * Get gainCreateur
	 *
	 * @return int
	 */
	public function getGainCreateur()
	{
		return $this->gainCreateur;
	}

	/**
	 * Set gainCreateur
	 *
	 * @param integer $gainCreateur
	 *
	 * @return Phrase
	 */
	public function setGainCreateur($gainCreateur)
	{
		$this->gainCreateur = $gainCreateur;

		return $this;
	}

	/**
	 * Met à jour les gains
	 * @param $gainCreateur
	 *
	 * @return $this
	 */
    public function updateGainCreateur($gainCreateur){
    	$this->gainCreateur += $gainCreateur;
    	if($this->gainCreateur < 0)
		    $this->gainCreateur = 0;
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
     * @return Phrase
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
     * @return Phrase
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
     * @return Phrase
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
     * @return Phrase
     */
    public function setModificateur(Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Add motAmbiguPhrase
     *
     * @param MotAmbiguPhrase $motAmbiguPhrase
     *
     * @return Phrase
     */
    public function addMotAmbiguPhrase(MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase[] = $motAmbiguPhrase;

        return $this;
    }

    /**
     * Remove motAmbiguPhrase
     *
     * @param MotAmbiguPhrase $motAmbiguPhrase
     */
    public function removeMotAmbiguPhrase(MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase->removeElement($motAmbiguPhrase);
    }

	/**
	 * Remove motsAmbigusPhrase
	 */
	public function removeMotsAmbigusPhrase()
	{
		$this->motsAmbigusPhrase = new ArrayCollection();
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

	public function getContenuAmb()
	{
		return preg_replace('#<amb id="([0-9]+)">(.*?)</amb>#', '<amb>$2</amb>', $this->getContenu());
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
	 * Set contenu
	 *
	 * @param string $contenu
	 *
	 * @return Phrase
	 */
	public function setContenu($contenu)
	{
		$this->contenu = $contenu;
		$this->setContenuPur($contenu);

		return $this;
	}

    /**
     * Add motsAmbigusPhrase
     *
     * @param MotAmbiguPhrase $motsAmbigusPhrase
     *
     * @return Phrase
     */
    public function addMotsAmbigusPhrase(MotAmbiguPhrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrase[] = $motsAmbigusPhrase;

        return $this;
    }

    /**
     * Add jAime
     *
     * @param JAime $jAime
     *
     * @return Phrase
     */
    public function addJAime(JAime $jAime)
    {
        $this->jAime[] = $jAime;

        return $this;
    }

    /**
     * Remove jAime
     *
     * @param JAime $jAime
     */
    public function removeJAime(JAime $jAime)
    {
        $this->jAime->removeElement($jAime);
    }

    /**
     * Get jAime
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJAime(){
	    return $this->jAime;
    }

	/**
	 * Get contenu pur
	 *
	 * @return string
	 */
	public function getContenuPur()
	{
		return $this->contenuPur;
	}

	/**
	 * Set contenu pur
	 *
	 * @param string $contenu
	 *
	 * @return Phrase
	 */
	public function setContenuPur($contenu)
	{
		$this->contenuPur = strip_tags($contenu);

		return $this;
	}

	/**
	 * Add party
	 *
	 * @param Partie $party
	 *
	 * @return Phrase
	 */
	public function addParty(Partie $party)
	{
		$this->parties[] = $party;

		return $this;
	}

	/**
	 * Remove party
	 *
	 * @param Partie $party
	 */
	public function removeParty(Partie $party)
	{
		$this->parties->removeElement($party);
	}

	/**
	 * Get parties
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getParties()
	{
		return $this->parties;
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
			$this->getContenuHTML(),
			$this->auteur->getUsername(),
			$this->dateCreation->getTimestamp(),
			$modificateur,
			$dateModification,
			$this->signale,
			$this->visible,
			$this->gainCreateur,
		);
	}

	/**
	 * AUTRES
	 */

	public function getContenuHTML()
	{
		return preg_replace('#<amb id="([0-9]+)">(.*?)</amb>#', '<b id="ma$1" class="ma color-red" title="Ce mot est ambigu (id : $1)">$2</b>',
			$this->getContenu());
	}

    public function __toString()
    {
        return $this->contenuPur;
    }

    public function isJouable($dureeAvantJouabilite)
    {
        $date = new \DateTime();
        $dateMin = $date->setTimestamp($date->getTimestamp() - $dureeAvantJouabilite);

        return $this->getVisible() && $this->getDateCreation() < $dateMin;
    }

}
