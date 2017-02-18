<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jugement
 *
 * @ORM\Table(name="jugement")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\JugementRepository")
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
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_deliberation", type="datetime", nullable=true)
     */
    private $dateDeliberation;

    /**
     * @var int
     *
     * @ORM\Column(name="id_objet", type="integer")
     */
    private $idObjet;

    /**
     * @var string
     *
     * @ORM\Column(name="type_jugement", type="string", length=8)
     */
    private $typeJugement;

    /**
     * @ORM\ManyToOne(targetEntity="CategorieJugement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorieJugement;

	/**
	 * @ORM\ManyToOne(targetEntity="TypeObjet")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $typeObjet;

	/**
	 * @ORM\ManyToOne(targetEntity="TypeVote")
	 */
	private $verdict;

	/**
	 * @ORM\ManyToOne(targetEntity="Membre")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $auteur;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
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
     * Set description
     *
     * @param string $description
     *
     * @return Jugement
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
     * @return Jugement
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
     * @return Jugement
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
     * Set idObjet
     *
     * @param integer $idObjet
     *
     * @return Jugement
     */
    public function setIdObjet($idObjet)
    {
        $this->idObjet = $idObjet;

        return $this;
    }

    /**
     * Get idObjet
     *
     * @return int
     */
    public function getIdObjet()
    {
        return $this->idObjet;
    }

    /**
     * Set typeJugement
     *
     * @param string $typeJugement
     *
     * @return Jugement
     */
    public function setTypeJugement($typeJugement)
    {
        $this->typeJugement = $typeJugement;

        return $this;
    }

    /**
     * Get typeJugement
     *
     * @return string
     */
    public function getTypeJugement()
    {
        return $this->typeJugement;
    }

    /**
     * Set categorieJugement
     *
     * @param \AmbigussBundle\Entity\CategorieJugement $categorieJugement
     *
     * @return Jugement
     */
    public function setCategorieJugement(\AmbigussBundle\Entity\CategorieJugement $categorieJugement)
    {
        $this->categorieJugement = $categorieJugement;

        return $this;
    }

    /**
     * Get categorieJugement
     *
     * @return \AmbigussBundle\Entity\CategorieJugement
     */
    public function getCategorieJugement()
    {
        return $this->categorieJugement;
    }

    /**
     * Set typeObjet
     *
     * @param \AmbigussBundle\Entity\TypeObjet $typeObjet
     *
     * @return Jugement
     */
    public function setTypeObjet(\AmbigussBundle\Entity\TypeObjet $typeObjet)
    {
        $this->typeObjet = $typeObjet;

        return $this;
    }

    /**
     * Get typeObjet
     *
     * @return \AmbigussBundle\Entity\TypeObjet
     */
    public function getTypeObjet()
    {
        return $this->typeObjet;
    }

    /**
     * Set verdict
     *
     * @param \AmbigussBundle\Entity\TypeVote $verdict
     *
     * @return Jugement
     */
    public function setVerdict(\AmbigussBundle\Entity\TypeVote $verdict = null)
    {
        $this->verdict = $verdict;

        return $this;
    }

    /**
     * Get verdict
     *
     * @return \AmbigussBundle\Entity\TypeVote
     */
    public function getVerdict()
    {
        return $this->verdict;
    }

    /**
     * Set auteur
     *
     * @param \AmbigussBundle\Entity\Membre $auteur
     *
     * @return Jugement
     */
    public function setAuteur(\AmbigussBundle\Entity\Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getAuteur()
    {
        return $this->auteur;
    }
}
