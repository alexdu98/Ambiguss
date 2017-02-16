<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Glose
 *
 * @ORM\Table(name="glose")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GloseRepository")
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
     * @ORM\Column(name="valeur", type="string", length=32)
     */
    private $valeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_Creation", type="datetime")
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
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="gloses")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;


      /**
      * @ORM\OneToMany(targetEntity="Reponse", mappedBy="glose")
      */
    private $reponses;


    /**
     * @ORM\OneToMany(targetEntity="Glose_mot_ambigu", mappedBy="glose")
     */
    private $GloseMotsAmbigus;



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
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
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
     * @return Glose
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
     * @return Glose
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
     * @return Glose
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
     * Set idAuteur
     *
     * @param integer $idAuteur
     *
     * @return Glose
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
     * @return Glose
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
    public function getMotsAmbigus()
    {
        return $this->motsAmbigus;
    }

    /**
     * @param mixed $motsAmbigus
     */
    public function setMotsAmbigus($motsAmbigus)
    {
        $this->motsAmbigus = $motsAmbigus;
    }

    /**
     * @return mixed
     */
    public function getReponses()
    {
        return $this->reponses;
    }

    /**
     * @param mixed $reponses
     */
    public function setReponses($reponses)
    {
        $this->reponses = $reponses;
    }

    /**
     * @return mixed
     */
    public function getGloseMotsAmbigus()
    {
        return $this->GloseMotsAmbigus;
    }

    /**
     * @param mixed $GloseMotsAmbigus
     */
    public function setGloseMotsAmbigus($GloseMotsAmbigus)
    {
        $this->GloseMotsAmbigus = $GloseMotsAmbigus;
    }



}
