<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membre
 *
 * @ORM\Table(name="membre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembreRepository")
 */
class Membre
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
     * @ORM\Column(name="pseudo", type="string", length=32)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=64)
     */
    private $mdp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="date")
     */
    private $dateInscription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateConexion", type="date")
     */
    private $dateConexion;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=16)
     */
    private $sexe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var int
     *
     * @ORM\Column(name="pointsClassement", type="integer")
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
     * @ORM\Column(name="cleOubliMdp", type="string", length=128, nullable=true)
     */
    private $cleOubliMdp;

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
     * @ORM\Column(name="commentairesBan", type="string", length=64, nullable=true)
     */
    private $commentairesBan;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeban", type="date", nullable=true)
     */
    private $dateDeban;


    /**
     * @var int
     *
     * @ORM\Column(name="id_niveau", type="integer", nullable=true)
     */
    
    private $idniveau;



    /**
     * @var int
     *
     * @ORM\Column(name="id_groupe", type="integer", nullable=true)
     */
    private $idgroupe;

    /**
     * @ORM\ManyToOne(targetEntity="Niveau", inversedBy="membres")
     * @ORM\JoinColumn(name="id_niveau", referencedColumnName="id")
     */
    private $niveau;

    /**
     * @ORM\OneToMany(targetEntity="Historique", mappedBy="membre")
     */
    private $historiques;


    
    /**
     * @ORM\OneToMany(targetEntity="Droit_membre", mappedBy="membre")
     */
    private $droit_membres;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="membres")
     * @ORM\JoinColumn(name="id_groupe", referencedColumnName="id")
     */
    private $groupe;

   
    /**
     * @ORM\OneToMany(targetEntity="Glose", mappedBy="membre")
     */
    private $gloses;

    /**
     * @ORM\OneToMany(targetEntity="Jeton", mappedBy="membre")
     */
    private $jetons;

    /**
     * @ORM\OneToMany(targetEntity="Newsletter", mappedBy="membre")
     */
    private $newsletters;

    /**
     * @ORM\OneToMany(targetEntity="Jugement", mappedBy="membre")
     */
    private $jugements;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="membre")
     */
    private $commentaires;


    /**
     * @ORM\OneToMany(targetEntity="Vote_jugement", mappedBy="membre")
     */
    private $voteJugements;

    /**
     * @ORM\OneToMany(targetEntity="Succes_membre", mappedBy="membre")
     */
    private $succesMembres;

    /**
     * @ORM\OneToMany(targetEntity="Aimer_phrase", mappedBy="membre")
     */
    private $AimerPhrases;



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
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Get mdp
     *
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
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
     * Get dateInscription
     *
     * @return \DateTime
     */
    public function getdateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set dateConexion
     *
     * @param \DateTime $dateConexion
     *
     * @return Membre
     */
    public function setdateConexion($dateConexion)
    {
        $this->dateConexion = $dateConexion;

        return $this;
    }

    /**
     * Get
     *
     * @return \DateTime
     */
    public function getDateConexion()
    {
        return $this->dateConexion;
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
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set datenaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Membre
     */
    public function setDatenaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

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
     * Get pointsClassement
     *
     * @return int
     */
    public function getPointsClassement()
    {
        return $this->pointsClassement;
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
     * Get credits
     *
     * @return int
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Set cleOubliMdp
     *
     * @param string $cleOubliMdp
     *
     * @return Membre
     */
    public function setcleOubliMdp($cleOubliMdp)
    {
        $this->cleOubliMdp = $cleOubliMdp;

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
     * Get newsletter
     *
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
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
     * Get banni
     *
     * @return bool
     */
    public function getBanni()
    {
        return $this->banni;
    }

    /**
     * Set commentairesBan
     *
     * @param string $commentairesBan
     *
     * @return Membre
     */
    public function setCommentairesBan($commentairesBan)
    {
        $this->commentairesBan = $commentairesBan;

        return $this;
    }

    /**
     * Get commentairesBan
     *
     * @return string
     */
    public function getCommentairesBan()
    {
        return $this->commentairesBan;
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
     * Get dateDeban
     *
     * @return \DateTime
     */
    public function getDateDeban()
    {
        return $this->dateDeban;
    }

    /**
     * @return mixed
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param mixed $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }

    /**
     * @return mixed
     */
    public function getIdniveau()
    {
        return $this->idniveau;
    }

    /**
     * @param mixed $idniveau
     */
    public function setIdniveau($idniveau)
    {
        $this->idniveau = $idniveau;
    }

   

    /**
     * @return int
     */
    public function getIdgroupe()
    {
        return $this->idgroupe;
    }

    /**
     * @param int $idgroupe
     */
    public function setIdgroupe($idgroupe)
    {
        $this->idgroupe = $idgroupe;
    }

    /**
     * @return mixed
     */
    public function getHistoriques()
    {
        return $this->historiques;
    }

    /**
     * @param mixed $historiques
     */
    public function setHistoriques($historiques)
    {
        $this->historiques = $historiques;
    }

    /**
     * @return mixed
     */
    public function getDroitMembres()
    {
        return $this->droit_membres;
    }

    /**
     * @param mixed $droit_membres
     */
    public function setDroitMembres($droit_membres)
    {
        $this->droit_membres = $droit_membres;
    }

    /**
     * @return mixed
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * @param mixed $groupe
     */
    public function setGroupe($groupe)
    {
        $this->groupe = $groupe;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->historiques = new \Doctrine\Common\Collections\ArrayCollection();
        $this->droit_membres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add historique
     *
     * @param \AppBundle\Entity\Historique $historique
     *
     * @return Membre
     */
    public function addHistorique(\AppBundle\Entity\Historique $historique)
    {
        $this->historiques[] = $historique;

        return $this;
    }

    /**
     * Remove historique
     *
     * @param \AppBundle\Entity\Historique $historique
     */
    public function removeHistorique(\AppBundle\Entity\Historique $historique)
    {
        $this->historiques->removeElement($historique);
    }

    /**
     * Add droitMembre
     *
     * @param \AppBundle\Entity\Droit_membre $droitMembre
     *
     * @return Membre
     */
    public function addDroitMembre(\AppBundle\Entity\Droit_membre $droitMembre)
    {
        $this->droit_membres[] = $droitMembre;

        return $this;
    }

    /**
     * Remove droitMembre
     *
     * @param \AppBundle\Entity\Droit_membre $droitMembre
     */
    public function removeDroitMembre(\AppBundle\Entity\Droit_membre $droitMembre)
    {
        $this->droit_membres->removeElement($droitMembre);
    }

    /**
     * @return mixed
     */
    public function getGloses()
    {
        return $this->gloses;
    }

    /**
     * @param mixed $gloses
     */
    public function setGloses($gloses)
    {
        $this->gloses = $gloses;
    }

    /**
     * @return mixed
     */
    public function getJetons()
    {
        return $this->jetons;
    }

    /**
     * @param mixed $jetons
     */
    public function setJetons($jetons)
    {
        $this->jetons = $jetons;
    }

    /**
     * @return mixed
     */
    public function getNewsletters()
    {
        return $this->newsletters;
    }

    /**
     * @param mixed $newsletters
     */
    public function setNewsletters($newsletters)
    {
        $this->newsletters = $newsletters;
    }

    /**
     * @return mixed
     */
    public function getJugements()
    {
        return $this->jugements;
    }

    /**
     * @param mixed $jugements
     */
    public function setJugements($jugements)
    {
        $this->jugements = $jugements;
    }

    /**
     * @return mixed
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param mixed $commentaires
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
    }

    /**
     * @return mixed
     */
    public function getVoteJugements()
    {
        return $this->voteJugements;
    }

    /**
     * @param mixed $voteJugements
     */
    public function setVoteJugements($voteJugements)
    {
        $this->voteJugements = $voteJugements;
    }

    /**
     * @return mixed
     */
    public function getSuccesMembres()
    {
        return $this->succesMembres;
    }

    /**
     * @param mixed $succesMembres
     */
    public function setSuccesMembres($succesMembres)
    {
        $this->succesMembres = $succesMembres;
    }

    /**
     * @return mixed
     */
    public function getAimerPhrases()
    {
        return $this->AimerPhrases;
    }

    /**
     * @param mixed $AimerPhrases
     */
    public function setAimerPhrases($AimerPhrases)
    {
        $this->AimerPhrases = $AimerPhrases;
    }

    
    

    /**
     * Add glose
     *
     * @param \AppBundle\Entity\Glose $glose
     *
     * @return Membre
     */
    public function addGlose(\AppBundle\Entity\Glose $glose)
    {
        $this->gloses[] = $glose;

        return $this;
    }

    /**
     * Remove glose
     *
     * @param \AppBundle\Entity\Glose $glose
     */
    public function removeGlose(\AppBundle\Entity\Glose $glose)
    {
        $this->gloses->removeElement($glose);
    }

    /**
     * Add jeton
     *
     * @param \AppBundle\Entity\Jeton $jeton
     *
     * @return Membre
     */
    public function addJeton(\AppBundle\Entity\Jeton $jeton)
    {
        $this->jetons[] = $jeton;

        return $this;
    }

    /**
     * Remove jeton
     *
     * @param \AppBundle\Entity\Jeton $jeton
     */
    public function removeJeton(\AppBundle\Entity\Jeton $jeton)
    {
        $this->jetons->removeElement($jeton);
    }

    /**
     * Add newsletter
     *
     * @param \AppBundle\Entity\Newsletter $newsletter
     *
     * @return Membre
     */
    public function addNewsletter(\AppBundle\Entity\Newsletter $newsletter)
    {
        $this->newsletters[] = $newsletter;

        return $this;
    }

    /**
     * Remove newsletter
     *
     * @param \AppBundle\Entity\Newsletter $newsletter
     */
    public function removeNewsletter(\AppBundle\Entity\Newsletter $newsletter)
    {
        $this->newsletters->removeElement($newsletter);
    }

    /**
     * Add jugement
     *
     * @param \AppBundle\Entity\Jugement $jugement
     *
     * @return Membre
     */
    public function addJugement(\AppBundle\Entity\Jugement $jugement)
    {
        $this->jugements[] = $jugement;

        return $this;
    }

    /**
     * Remove jugement
     *
     * @param \AppBundle\Entity\Jugement $jugement
     */
    public function removeJugement(\AppBundle\Entity\Jugement $jugement)
    {
        $this->jugements->removeElement($jugement);
    }

    /**
     * Add commentaire
     *
     * @param \AppBundle\Entity\Commentaire $commentaire
     *
     * @return Membre
     */
    public function addCommentaire(\AppBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \AppBundle\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\AppBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Add voteJugement
     *
     * @param \AppBundle\Entity\Vote_jugement $voteJugement
     *
     * @return Membre
     */
    public function addVoteJugement(\AppBundle\Entity\Vote_jugement $voteJugement)
    {
        $this->voteJugements[] = $voteJugement;

        return $this;
    }

    /**
     * Remove voteJugement
     *
     * @param \AppBundle\Entity\Vote_jugement $voteJugement
     */
    public function removeVoteJugement(\AppBundle\Entity\Vote_jugement $voteJugement)
    {
        $this->voteJugements->removeElement($voteJugement);
    }

    /**
     * Add succesMembre
     *
     * @param \AppBundle\Entity\Succes_membre $succesMembre
     *
     * @return Membre
     */
    public function addSuccesMembre(\AppBundle\Entity\Succes_membre $succesMembre)
    {
        $this->succesMembres[] = $succesMembre;

        return $this;
    }

    /**
     * Remove succesMembre
     *
     * @param \AppBundle\Entity\Succes_membre $succesMembre
     */
    public function removeSuccesMembre(\AppBundle\Entity\Succes_membre $succesMembre)
    {
        $this->succesMembres->removeElement($succesMembre);
    }

    /**
     * Add aimerPhrase
     *
     * @param \AppBundle\Entity\Aimer_phrase $aimerPhrase
     *
     * @return Membre
     */
    public function addAimerPhrase(\AppBundle\Entity\Aimer_phrase $aimerPhrase)
    {
        $this->AimerPhrases[] = $aimerPhrase;

        return $this;
    }

    /**
     * Remove aimerPhrase
     *
     * @param \AppBundle\Entity\Aimer_phrase $aimerPhrase
     */
    public function removeAimerPhrase(\AppBundle\Entity\Aimer_phrase $aimerPhrase)
    {
        $this->AimerPhrases->removeElement($aimerPhrase);
    }
}
