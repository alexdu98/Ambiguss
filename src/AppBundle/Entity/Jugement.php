<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jugement
 *
 * @ORM\Table(name="jugement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\jugementRepository")
 */
class Jugement
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
     * @ORM\Column(name="description", type="string", length=256)
     */
    private $description;

    /**
     * @var enum
     *
     * @ORM\Column(name="type", type="string", length=256)
     */
    private $type;

    /**
     * @var enum
     *
     * @ORM\Column(name="categorie", type="string", length=256)
     */
    private $categorie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeliberation", type="datetime")
     */
    private $dateDeliberation;

    /**
     * @var int
     *
     * @ORM\Column(name="idAuteur", type="integer", nullable=true)
     */
    private $idAuteur;


    /**
     * @var enum
     * @ORM\Column(name="resultat", type="string", length=256)
     */
    private $resultat;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="jugements")
     * @ORM\JoinColumn(name="membre_id", referencedColumnName="id")
     */
    private $membre;


    /**
     * @ORM\OneToMany(targetEntity="Vote_jugement", mappedBy="jugement")
     */
    private $voteJugements;


    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="jugement")
     */
    private $Commentaires;



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
     * Set description
     *
     * @param string $description
     *
     * @return jugement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return jugement
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
     * Set dateDeliberation
     *
     * @param \DateTime $dateDeliberation
     *
     * @return jugement
     */
    public function setDateDeliberation($dateDeliberation)
    {
        $this->dateDeliberation = $dateDeliberation;

        return $this;
    }

    /**
     * Get dateDeliberation
     *
     * @return \DateTime
     */
    public function getDateDeliberation()
    {
        return $this->dateDeliberation;
    }

    /**
     * Set idAuteur
     *
     * @param integer $idAuteur
     *
     * @return jugement
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * @param mixed $resultat
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;
    }

    /**
     * @return enum
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param enum $categorie
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
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
    public function getCommentaires()
    {
        return $this->Commentaires;
    }

    /**
     * @param mixed $Commentaires
     */
    public function setCommentaires($Commentaires)
    {
        $this->Commentaires = $Commentaires;
    }
    
    
    
}
