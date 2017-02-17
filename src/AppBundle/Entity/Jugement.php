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
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="jugements")
     * @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
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
	 * @ORM\ManyToOne(targetEntity="Enum_type_vote")
	 * @ORM\JoinColumn(name="id_enum_type_vote", referencedColumnName="id", nullable=true)
	 */
	private $typeVote;

	/**
	 * @ORM\ManyToOne(targetEntity="Enum_type_objet")
	 * @ORM\JoinColumn(name="id_enum_type_objet", referencedColumnName="id", nullable=false)
	 */
    private $typeObjet;

	/**
	 * @ORM\ManyToOne(targetEntity="Enum_categorie_jugement")
	 * @ORM\JoinColumn(name="id_enum_categorie_jugement", referencedColumnName="id", nullable=false)
	 */
	private $categorieJugement;



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
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->voteJugements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Commentaires = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add voteJugement
     *
     * @param \AppBundle\Entity\Vote_jugement $voteJugement
     *
     * @return Jugement
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
     * Add commentaire
     *
     * @param \AppBundle\Entity\Commentaire $commentaire
     *
     * @return Jugement
     */
    public function addCommentaire(\AppBundle\Entity\Commentaire $commentaire)
    {
        $this->Commentaires[] = $commentaire;

        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \AppBundle\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\AppBundle\Entity\Commentaire $commentaire)
    {
        $this->Commentaires->removeElement($commentaire);
    }

    /**
     * Set typeVote
     *
     * @param \AppBundle\Entity\Enum_type_vote $typeVote
     *
     * @return Jugement
     */
    public function setTypeVote(\AppBundle\Entity\Enum_type_vote $typeVote = null)
    {
        $this->typeVote = $typeVote;

        return $this;
    }

    /**
     * Get typeVote
     *
     * @return \AppBundle\Entity\Enum_type_vote
     */
    public function getTypeVote()
    {
        return $this->typeVote;
    }

    /**
     * Set typeObjet
     *
     * @param \AppBundle\Entity\Enum_type_objet $typeObjet
     *
     * @return Jugement
     */
    public function setTypeObjet(\AppBundle\Entity\Enum_type_objet $typeObjet)
    {
        $this->typeObjet = $typeObjet;

        return $this;
    }

    /**
     * Get typeObjet
     *
     * @return \AppBundle\Entity\Enum_type_objet
     */
    public function getTypeObjet()
    {
        return $this->typeObjet;
    }

    /**
     * Set categorieJugement
     *
     * @param \AppBundle\Entity\Enum_categorie_jugement $categorieJugement
     *
     * @return Jugement
     */
    public function setCategorieJugement(\AppBundle\Entity\Enum_categorie_jugement $categorieJugement)
    {
        $this->categorieJugement = $categorieJugement;

        return $this;
    }

    /**
     * Get categorieJugement
     *
     * @return \AppBundle\Entity\Enum_categorie_jugement
     */
    public function getCategorieJugement()
    {
        return $this->categorieJugement;
    }
}
