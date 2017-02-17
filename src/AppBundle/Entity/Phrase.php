<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phrase
 *
 * @ORM\Table(name="phrase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhraseRepository")
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
     * @ORM\Column(name="date_modification", type="datetime")
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
     * @var int
     *
     * @ORM\Column(name="id_modificateur", type="integer", nullable=true)
     */
    private $idModificateur;

    /**
     * @var int
     *
     * @ORM\Column(name="id_auteur", type="integer", nullable=true)
     */
    private $idAuteur;

    /**
     * @ORM\OneToMany(targetEntity="Reponse", mappedBy="phrase")
     */
    private $Reponses;

    /**
     * @ORM\OneToMany(targetEntity="Mot_ambigu_phrase", mappedBy="phrase")
     */
    private $motsAmbigusPhrases;

    /**
     * @ORM\OneToMany(targetEntity="Aimer_phrase", mappedBy="phrase")
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
     * Set idModificateur
     *
     * @param integer $idModificateur
     *
     * @return Phrase
     */
    public function setIdModificateur($idModificateur)
    {
        $this->idModificateur = $idModificateur;

        return $this;
    }

    /**
     * Get idModificateur
     *
     * @return int
     */
    public function getIdModificateur()
    {
        return $this->idModificateur;
    }

    /**
     * Set idAuteur
     *
     * @param integer $idAuteur
     *
     * @return Phrase
     */
    public function setIdAuteur($idAuteur)
    {
        $this->idAuteur = $idAuteur;

        return $this;
    }

    /**
     * Get idAuteur
     *
     * @return int
     */
    public function getIdAuteur()
    {
        return $this->idAuteur;
    }

    /**
     * @return mixed
     */
    public function getReponses()
    {
        return $this->Reponses;
    }

    /**
     * @param mixed $Reponses
     */
    public function setReponses($Reponses)
    {
        $this->Reponses = $Reponses;
    }

    /**
     * @return mixed
     */
    public function getMotsAmbigusPhrases()
    {
        return $this->motsAmbigusPhrases;
    }

    /**
     * @param mixed $motsAmbigusPhrases
     */
    public function setMotsAmbigusPhrases($motsAmbigusPhrases)
    {
        $this->motsAmbigusPhrases = $motsAmbigusPhrases;
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
     * Constructor
     */
    public function __construct()
    {
        $this->Reponses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->motsAmbigusPhrases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->AimerPhrases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     *
     * @return Phrase
     */
    public function addReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->Reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     */
    public function removeReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->Reponses->removeElement($reponse);
    }

    /**
     * Add motsAmbigusPhrase
     *
     * @param \AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase
     *
     * @return Phrase
     */
    public function addMotsAmbigusPhrase(\AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrases[] = $motsAmbigusPhrase;

        return $this;
    }

    /**
     * Remove motsAmbigusPhrase
     *
     * @param \AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase
     */
    public function removeMotsAmbigusPhrase(\AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrases->removeElement($motsAmbigusPhrase);
    }

    /**
     * Add aimerPhrase
     *
     * @param \AppBundle\Entity\Aimer_phrase $aimerPhrase
     *
     * @return Phrase
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
