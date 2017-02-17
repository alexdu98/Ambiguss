<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentaireRepository")
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
     * @ORM\Column(name="id_commentaire_parent", type="integer", nullable=true)
     */
    private $idCommentaireParent;

    /**
     * @var int
     *
     * @ORM\Column(name="id_jugement", type="integer", nullable=true)
     */
    private $idJugement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_auteur", type="integer", nullable=true)
     */
    private $idAuteur;

    /**
     * @var int
     *
     * @ORM\Column(name="id_modificateur", type="integer", nullable=true)
     */
    private $idModificateur;

    /**
     * @var int
     *
     * @ORM\Column(name="id_phrase", type="integer", nullable=true)
     */
    private $idPhrase;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="Commentaires")
     * @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="Jugement", inversedBy="commentaires")
     * @ORM\JoinColumn(name="id_jugement", referencedColumnName="id")
     */
    private $jugement;

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
     * Set idCommentaireParent
     *
     * @param integer $idCommentaireParent
     *
     * @return Commentaire
     */
    public function setIdCommentaireParent($idCommentaireParent)
    {
        $this->idCommentaireParent = $idCommentaireParent;

        return $this;
    }

    /**
     * Get idCommentaireParent
     *
     * @return int
     */
    public function getIdCommentaireParent()
    {
        return $this->idCommentaireParent;
    }

    /**
     * Set idJugement
     *
     * @param integer $idJugement
     *
     * @return Commentaire
     */
    public function setIdJugement($idJugement)
    {
        $this->idJugement = $idJugement;

        return $this;
    }

    /**
     * Get idJugement
     *
     * @return int
     */
    public function getIdJugement()
    {
        return $this->idJugement;
    }

    /**
     * Set idAuteur
     *
     * @param integer $idAuteur
     *
     * @return Commentaire
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
     * Set idModificateur
     *
     * @param integer $idModificateur
     *
     * @return Commentaire
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
     * Set idPhrase
     *
     * @param integer $idPhrase
     *
     * @return Commentaire
     */
    public function setIdPhrase($idPhrase)
    {
        $this->idPhrase = $idPhrase;

        return $this;
    }

    /**
     * Get idPhrase
     *
     * @return int
     */
    public function getIdPhrase()
    {
        return $this->idPhrase;
    }

    /**
     * @return mixed
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * @param mixed $membre
     */
    public function setMembre($membre)
    {
        $this->membre = $membre;
    }

    /**
     * @return mixed
     */
    public function getJugement()
    {
        return $this->jugement;
    }

    /**
     * @param mixed $jugement
     */
    public function setJugement($jugement)
    {
        $this->jugement = $jugement;
    }

}
