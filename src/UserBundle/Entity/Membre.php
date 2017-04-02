<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Membre
 *
 * @ORM\Table(name="membre", indexes={
 *     @ORM\Index(name="IDX_MEMBRE_POINTSCLASSEMENT", columns={"points_classement"}),
 *     @ORM\Index(name="IDX_MEMBRE_CREDITS", columns={"credits"})
 * })
 * @ORM\Entity(repositoryClass="UserBundle\Repository\MembreRepository")
 * @UniqueEntity(fields="pseudo", message="Ce pseudo existe déjà.")
 * @UniqueEntity(fields="email", message="Cette adresse mail existe déjà.")
 */
class Membre implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="pseudo", type="string", length=32, nullable=true, unique=true)
     *
     * @Assert\Regex(
     *     pattern = "#^$|^[a-zA-Z0-9-_]{3,32}$#",
     *     message = "Pseudo invalide. (3 à 32 caractères alphanumérique (- et _ autorisé))"
     * )
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, unique=true)
     *
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas valide.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=72, nullable=true)
     *
     * @Assert\Length(
     *     min="6",
     *     max="72",
     *     minMessage="Le mot de passe doit faire au moins 6 caractères.",
     *     maxMessage="Le mot de passe doit faire 72 caractères maximum."
     * )
     */
    private $mdp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="datetime")
     */
    private $dateInscription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_connexion", type="datetime", nullable=true)
     */
    private $dateConnexion;

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
     *
     * @Assert\DateTime()
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
     * @ORM\Column(name="credits", type="integer")
     */
    private $credits;

    /**
     * @var string
     *
     * @ORM\Column(name="cle_oubli_mdp", type="string", length=128, nullable=true, unique=true)
     */
    private $cleOubliMdp;

    /**
     * @var bool
     *
     * @ORM\Column(name="newsletter", type="boolean")
     *
     * @Assert\Type(
     *      type="bool"
     * )
     * @Assert\NotNull()
     */
    private $newsletter;

    /**
     * @var bool
     *
     * @ORM\Column(name="banni", type="boolean")
     *
     * @Assert\Type(
     *      type="bool"
     * )
     * @Assert\NotNull()
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
     *
     * @Assert\DateTime()
     */
    private $dateDeban;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="actif", type="boolean")
	 *
	 * @Assert\Type(
	 *      type="bool"
	 * )
	 * @Assert\NotNull()
	 */
	private $actif;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="id_facebook", type="string", length=255, nullable=true, unique=true)
	 */
	private $idFacebook;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="id_twitter", type="string", length=255, nullable=true, unique=true)
	 */
	private $idTwitter;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity="Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @ORM\OneToMany(targetEntity="MembreRole", mappedBy="membre")
     */
    private $membreRoles;

	/**
	 * @ORM\OneToMany(targetEntity="AmbigussBundle\Entity\Phrase", mappedBy="auteur")
	 */
    private $phrases;

	/**
	 * @ORM\OneToMany(targetEntity="AmbigussBundle\Entity\Glose", mappedBy="auteur")
	 */
    private $gloses;

	/**
	 * @ORM\OneToMany(targetEntity="AmbigussBundle\Entity\MotAmbigu", mappedBy="auteur")
	 */
	private $motsAmbigus;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dateInscription = new \DateTime();
		$this->pointsClassement = 0;
		$this->credits = 0;
		$this->newsletter = true;
		$this->banni = false;
		$this->actif = false;
		$this->phrases = new ArrayCollection();
		$this->gloses = new ArrayCollection();
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
     * Get email
     *
     * @return string
     */
	public function getEmail()
    {
	    return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Membre
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
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
     * Get dateConnexion
     *
     * @return \DateTime
     */
	public function getDateConnexion()
    {
	    return $this->dateConnexion;
    }

    /**
     * Set dateConnexion
     *
     * @param \DateTime $dateConnexion
     *
     * @return Membre
     */
    public function setDateConnexion($dateConnexion)
    {
        $this->dateConnexion = $dateConnexion;

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
     * Get cleOubliMdp
     *
     * @return string
     */
	public function getCleOubliMdp()
    {
	    return $this->cleOubliMdp;
    }

    /**
     * Set cleOublimdp
     *
     * @param string $cleOubliMdp
     *
     * @return Membre
     */
    public function setCleOubliMdp($cleOubliMdp)
    {
        $this->cleOubliMdp = $cleOubliMdp;

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
	 * Get idFacebook
	 *
	 * @return string
	 */
	public function getIdFacebook()
	{
		return $this->idFacebook;
	}

	/**
	 * Set idFacebook
	 *
	 * @param string $idFacebook
	 *
	 * @return Membre
	 */
	public function setIdFacebook($idFacebook)
	{
		$this->idFacebook = $idFacebook;

		return $this;
	}

	/**
	 * Get idTwitter
	 *
	 * @return string
	 */
	public function getIdTwitter()
	{
		return $this->idTwitter;
	}

	/**
	 * Set idTwitter
	 *
	 * @param string $idTwitter
	 *
	 * @return Membre
	 */
	public function setIdTwitter($idTwitter)
	{
		$this->idTwitter = $idTwitter;

		return $this;
	}

	/**
	 * Get niveau
	 *
	 * @return \UserBundle\Entity\Niveau
	 */
	public function getNiveau()
    {
	    return $this->niveau;
    }

    /**
     * Set niveau
     *
     * @param \UserBundle\Entity\Niveau $niveau
     *
     * @return Membre
     */
    public function setNiveau(\UserBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Add membreRole
     *
     * @param \UserBundle\Entity\MembreRole $membreRole
     *
     * @return Membre
     */
    public function addMembreRole(\UserBundle\Entity\MembreRole $membreRole)
    {
	    $this->membreRoles[] = $membreRole;

	    $membreRole->setMembre($this);

	    return $this;
    }

	/**
	 * Remove membreRole
	 *
     * @param \UserBundle\Entity\MembreRole $membreRole
	 */
    public function removeMembreRole(\UserBundle\Entity\MembreRole $membreRole)
    {
	    $this->membreRoles->removeElement($membreRole);
    }

	/**
	 * Get roles (droits)
	 *
	 * @return array
	 */
	public function getRoles()
    {
		$roles = ["ROLE_VISITEUR"];

        $roles = array_merge($roles, $this->getGroupe()->getRoles());

        foreach ($this->getMembreRoles() as $membreRole) {
            $roles[] = $membreRole->getRole()->getNom();
        }

        return $roles;
	}

	/**
	 * Get groupe
	 *
	 * @return \UserBundle\Entity\Groupe
	 */
	public function getGroupe()
	{
		return $this->groupe;
	}

	/**
	 * Set groupe
	 *
	 * @param \UserBundle\Entity\Groupe $groupe
	 *
	 * @return Membre
	 */
	public function setGroupe(\UserBundle\Entity\Groupe $groupe)
	{
		$this->groupe = $groupe;

		return $this;
	}

	/**
	 * Get membreRoles
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getMembreRoles()
	{
		return $this->membreRoles;
	}

	/**
	 * Get password (mdp)
	 *
	 * @return string
	 */
	public function getPassword(){
		return $this->getMdp();
	}

	/**
	 * Get mdp
	 *
	 * @return string
	 */
	public function getMdp()
	{
		return $this->mdp;
	}

	/**
	 * Set mdp
	 *
	 * @param string $mdp
	 *
	 * @return Membre
	 */
	public function setMdp($mdp)
	{
		$this->mdp = $mdp;

		return $this;
	}

	/**
	 * Get salt
	 *
	 * @return null (no use with BCryptEncoder)
     */
	public function getSalt(){
		return null;
	}

	/**
	 * Get username (pseudo)
	 *
	 * @return string
	 */
	public function getUsername(){
		return $this->getPseudo();
	}

	/**
	 * Get pseudo
	 *
	 * @return string
	 */
	public function getPseudo()
	{
		return $this->pseudo;
	}

	/**
	 * Set pseudo
	 *
	 * @param string $pseudo
	 *
	 * @return Membre
	 */
	public function setPseudo($pseudo)
	{
		$this->pseudo = $pseudo;

		return $this;
	}

	/**
	 * IMPLEMENTS AdvancedUserInterface
	 */

	public function eraseCredentials(){}

	/**
	 * Check if user account has expired
	 *
	 * @return bool
	 */
	public function isAccountNonExpired(){
		return true;
	}

	/**
	 * Check if user is locked
	 *
	 * @return bool
	 */
	public function isAccountNonLocked(){
		return !$this->getBanni();
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

		return $this;
	}

	/**
	 * Check if user credentials has expired
	 *
	 * @return bool
	 */
	public function isCredentialsNonExpired(){
		return true;
	}

	/**
	 * Check if user is enabled
	 *
	 * @return bool
	 */
	public function isEnabled(){
		return $this->getActif();
	}

	/**
	 * Get actif
	 *
	 * @return boolean
	 */
	public function getActif()
	{
		return $this->actif;
	}

	/**
	 * Set actif
	 *
	 * @param boolean $actif
	 *
	 * @return Membre
	 */
	public function setActif($actif)
	{
		$this->actif = $actif;

		return $this;
	}

	/**
	 * IMPLEMENTS Serializable
	 */

	public function serialize(){
		return serialize(array(
			$this->id,
			$this->pseudo,
            $this->email,
			$this->mdp,
			$this->banni,
			$this->actif
		));
	}

	public function unserialize($serialized){
		list (
			$this->id,
			$this->pseudo,
            $this->email,
			$this->mdp,
			$this->banni,
			$this->actif
			) = unserialize($serialized);
	}


    /**
     * AUTRES
     */

    /**
     * Génère, initialise et retourne une clé
     *
     * @return string
     */
    public function generateCle(){
        $cle = hash('sha256', uniqid(rand(), true) . "N1TDf^%PEc!G*s$");
        $this->setCleOubliMdp($cle);
        return $cle;
    }

    /**
     * Add phrase
     *
     * @param \AmbigussBundle\Entity\Phrase $phrase
     *
     * @return Membre
     */
    public function addPhrase(\AmbigussBundle\Entity\Phrase $phrase)
    {
        $this->phrases[] = $phrase;

        return $this;
    }

    /**
     * Remove phrase
     *
     * @param \AmbigussBundle\Entity\Phrase $phrase
     */
    public function removePhrase(\AmbigussBundle\Entity\Phrase $phrase)
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
    	if($this->pointsClassement < 0)
		    $this->pointsClassement = 0;
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
     * @param \AmbigussBundle\Entity\Glose $glose
     *
     * @return Membre
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
	 * Add motsAmbigus
	 *
	 * @param \AmbigussBundle\Entity\MotAmbigu $motsAmbigus
	 *
	 * @return Membre
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
}
