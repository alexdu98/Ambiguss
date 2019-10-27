<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Membre
 *
 * @ORM\Table(name="membre", indexes={
 *     @ORM\Index(name="IDX_MEMBRE_POINTSCLASSEMENT", columns={"points_classement"}),
 *     @ORM\Index(name="IDX_MEMBRE_CREDITS", columns={"credits"}),
 *     @ORM\Index(name="IDX_MEMBRE_DATEINSCRIPTION", columns={"date_inscription"}),
 *     @ORM\Index(name="IDX_MEMBRE_SEXE", columns={"sexe"}),
 *     @ORM\Index(name="IDX_MEMBRE_DATENAISSANCE", columns={"date_naissance"}),
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembreRepository")
 * @UniqueEntity(
 *     fields={"emailCanonical"},
 *     errorPath="email",
 *     message="L'email {{ value }} est déjà utilisé"
 * )
 * @UniqueEntity(
 *     fields={"usernameCanonical"},
 *     errorPath="username",
 *     message="Le pseudo {{ value }} est déjà utilisé"
 * )
 */
class Membre extends User implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime")
     */
    private $dateInscription;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=8, nullable=true)
     *
     * @Assert\Regex(
     *     pattern = "#^Homme$|^Femme$#",
     *     message = "Sexe invalide. (Homme ou Femme)"
     * )
     */
    private $sexe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_naissance", type="datetime", nullable=true)
     */
    private $dateNaissance;

    /**
     * @var int
     *
     * @ORM\Column(name="points_classement", type="integer")
     */
    private $pointsClassement;

    /**
     * @var int
     *
     * @ORM\Column(name="points_classement_mensuel", type="integer")
     */
    private $pointsClassementMensuel;

    /**
     * @var int
     *
     * @ORM\Column(name="points_classement_hebdomadaire", type="integer")
     */
    private $pointsClassementHebdomadaire;

    /**
     * @var int
     *
     * @ORM\Column(name="credits", type="integer")
     */
    private $credits;

    /**
     * @var bool
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    private $newsletter;

    /**
     * @var bool
     *
     * @ORM\Column(name="banni", type="boolean")
     */
    private $banni;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire_ban", type="string", length=128, nullable=true)
     */
    private $commentaireBan;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_deban", type="datetime", nullable=true)
     */
    private $dateDeban;

	/**
	 * @ORM\OneToMany(targetEntity="Phrase", mappedBy="auteur")
	 */
    private $phrases;

	/**
	 * @ORM\OneToMany(targetEntity="Glose", mappedBy="auteur")
	 */
    private $gloses;

	/**
	 * @ORM\OneToMany(targetEntity="MotAmbigu", mappedBy="auteur")
	 */
	private $motsAmbigus;

	/**
	 * @ORM\OneToMany(targetEntity="Historique", mappedBy="membre")
	 */
	private $historiques;

    /**
     * @ORM\OneToMany(targetEntity="Partie", mappedBy="joueur", cascade={"persist"})
     */
    private $parties;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Groupe", cascade={"persist"})
     * @ORM\JoinTable(name="membre_groupe",
     *      joinColumns={@ORM\JoinColumn(name="membre_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="groupe_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true, unique=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true, unique=true)
     */
    private $twitterId;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true, unique=true)
     */
    private $googleId;

    /**
     * @ORM\Column(name="renamable", type="boolean")
     */
    private $renamable;

    /**
     * @ORM\Column(name="service_creation", type="boolean")
     */
    private $serviceCreation;

    /**
     * @var bool
     *
     * @ORM\Column(name="signale", type="boolean")
     */
    private $signale;


	/**
	 * Constructor
	 */
	public function __construct()
	{
	    parent::__construct();
		$this->dateInscription = new \DateTime();
		$this->pointsClassement = 0;
		$this->credits = 0;
		$this->newsletter = true;
        $this->banni = false;
        $this->renamable = false;
        $this->serviceCreation = false;
        $this->signale = false;
		$this->phrases = new ArrayCollection();
		$this->gloses = new ArrayCollection();
		$this->motsAmbigus = new ArrayCollection();
		$this->historiques = new ArrayCollection();

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
     * Get dateInscription
     *
     * @return \DateTime
     */
	public function getDateInscription()
    {
	    return $this->dateInscription;
    }

    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     *
     * @return Membre
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
	public function getSexe()
    {
	    return $this->sexe;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Membre
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
	public function getDateNaissance()
    {
	    return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Membre
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get pointsClassement
     *
     * @return int
     */
	public function getPointsClassement()
    {
	    return $this->pointsClassement;
    }

    /**
     * Set pointsClassement
     *
     * @param integer $pointsClassement
     *
     * @return Membre
     */
    public function setPointsClassement($pointsClassement)
    {
        $this->pointsClassement = $pointsClassement;

        return $this;
    }

    /**
     * Get pointsClassementMensuel
     *
     * @return int
     */
    public function getPointsClassementMensuel()
    {
        return $this->pointsClassementMensuel;
    }

    /**
     * Set pointsClassementMensuel
     *
     * @param integer $pointsClassementMensuel
     *
     * @return Membre
     */
    public function setPointsClassementMensuel($pointsClassementMensuel)
    {
        $this->pointsClassementMensuel = $pointsClassementMensuel;

        return $this;
    }

    /**
     * Get pointsClassementHebdomadaire
     *
     * @return int
     */
    public function getPointsClassementHebdomadaire()
    {
        return $this->pointsClassementHebdomadaire;
    }

    /**
     * Set pointsClassementHebdomadaire
     *
     * @param integer $pointsClassementHebdomadaire
     *
     * @return Membre
     */
    public function setPointsClassementHebdomadaire($pointsClassementHebdomadaire)
    {
        $this->pointsClassementHebdomadaire = $pointsClassementHebdomadaire;

        return $this;
    }

    /**
     * Get credits
     *
     * @return int
     */
	public function getCredits()
    {
	    return $this->credits;
    }

    /**
     * Set credits
     *
     * @param integer $credits
     *
     * @return Membre
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return bool
     */
	public function getNewsletter()
    {
	    return $this->newsletter;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     *
     * @return Membre
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get commentaireBan
     *
     * @return string
     */
    public function getCommentaireBan()
    {
        return $this->commentaireBan;
    }

    /**
     * Set commentaireBan
     *
     * @param string $commentaireBan
     *
     * @return Membre
     */
    public function setCommentaireBan($commentaireBan)
    {
        $this->commentaireBan = $commentaireBan;
        return $this;
    }

    /**
     * Get dateDeban
     *
     * @return \DateTime
     */
    public function getDateDeban()
    {
        return $this->dateDeban;
    }

    /**
     * Set dateDeban
     *
     * @param \DateTime $dateDeban
     *
     * @return Membre
     */
    public function setDateDeban($dateDeban)
    {
        $this->dateDeban = $dateDeban;
        return $this;
    }
    /**
     * Get banni
     *
     * @return bool
     */
    public function getBanni()
    {
        return $this->banni;
    }
    /**
     * Set banni
     *
     * @param boolean $banni
     *
     * @return Membre
     */
    public function setBanni($banni)
    {
        $this->banni = $banni;

        if (!$banni) {
            $this->setCommentaireBan(null);
            $this->setDateDeban(null);
        }

        return $this;
    }

    /**
     * Add phrase
     *
     * @param Phrase $phrase
     *
     * @return Membre
     */
    public function addPhrase(Phrase $phrase)
    {
        $this->phrases[] = $phrase;

        return $this;
    }

    /**
     * Remove phrase
     *
     * @param Phrase $phrase
     */
    public function removePhrase(Phrase $phrase)
    {
        $this->phrases->removeElement($phrase);
    }

    /**
     * Get phrases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

	/**
	 * Met à jour le nombre de points
	 * @param $points
	 *
	 * @return $this
	 */
    public function updatePoints($points){
    	$this->pointsClassement += $points;
    	$this->pointsClassementMensuel += $points;
    	$this->pointsClassementHebdomadaire += $points;

    	if($this->pointsClassement < 0)
		    $this->pointsClassement = 0;
        if($this->pointsClassementMensuel < 0)
            $this->pointsClassementMensuel = 0;
        if($this->pointsClassementHebdomadaire < 0)
            $this->pointsClassementHebdomadaire = 0;

    	return $this;
    }

	/**
	 * Met à jour le nombre de crédits
	 * @param $credits
	 *
	 * @return $this
	 */
    public function updateCredits($credits){
	    $this->credits += $credits;
	    if($this->credits < 0)
		    $this->credits = 0;
	    return $this;
    }

    /**
     * Add glose
     *
     * @param Glose $glose
     *
     * @return Membre
     */
    public function addGlose(Glose $glose)
    {
        $this->gloses[] = $glose;

        return $this;
    }

    /**
     * Remove glose
     *
     * @param Glose $glose
     */
    public function removeGlose(Glose $glose)
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
	 * Add motsAmbigus
	 *
	 * @param MotAmbigu $motsAmbigus
	 *
	 * @return Membre
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
	 * Add historique
	 *
	 * @param Historique $historique
	 *
	 * @return Membre
	 */
	public function addHistorique(Historique $historique)
	{
		$this->historiques[] = $historique;

		return $this;
	}

	/**
	 * Remove historique
	 *
	 * @param Historique $historique
	 */
	public function removeHistorique(Historique $historique)
	{
		$this->historiques->removeElement($historique);
	}

	/**
	 * Get historiques
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getHistoriques()
	{
		return $this->historiques;
	}

    /**
     * Add partie
     *
     * @param Partie $partie
     *
     * @return Membre
     */
    public function addPartie(Partie $partie)
    {
        $this->parties[] = $partie;

        return $this;
    }

    /**
     * Remove partie
     *
     * @param Partie $partie
     */
    public function removePartie(Partie $partie)
    {
        $this->parties->removeElement($partie);
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
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    public function isAccountNonLocked(){
        // Si non bannis, ou bannis mais pas à vie et que la date de fin soit passée
        if(!$this->getBanni() || ($this->getDateDeban() != null && $this->getDateDeban() <= new \DateTime()))
            return true;
        return false;
    }

    /**
     * Set renamable
     *
     * @param boolean $renamable
     *
     * @return Membre
     */
    public function setRenamable($renamable)
    {
        $this->renamable = $renamable;

        return $this;
    }

    /**
     * Get renamable
     *
     * @return boolean
     */
    public function getRenamable()
    {
        return $this->renamable;
    }

    /**
     * Set serviceCreation
     *
     * @param boolean $serviceCreation
     *
     * @return Membre
     */
    public function setServiceCreation($serviceCreation)
    {
        $this->serviceCreation = $serviceCreation;

        return $this;
    }

    /**
     * Get serviceCreation
     *
     * @return boolean
     */
    public function isServiceCreation()
    {
        return $this->serviceCreation;
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
     * @return Membre
     */
    public function setSignale($signale)
    {
        $this->signale = $signale;

        return $this;
    }


    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'signale' => $this->getSignale(),
            'renomable' => $this->getRenamable(),
            'banni' => $this->getBanni(),
            'commentaireBan' => $this->getCommentaireBan(),
            'dateDeban' => $this->getDateDeban() ? $this->getDateDeban()->format('U') : null
        );
    }
}
