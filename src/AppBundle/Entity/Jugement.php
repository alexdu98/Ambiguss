<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jugement
 *
 * @ORM\Table(name="jugement", indexes={
 *     @ORM\Index(name="IDX_JUGEMENT_DATECREATION", columns={"date_creation"}),
 *     @ORM\Index(name="IDX_JUGEMENT_DATEDELIBERATION", columns={"date_deliberation"}),
 *     @ORM\Index(name="IDX_JUGEMENT_IDOBJET", columns={"id_objet"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JugementRepository")
 */
class Jugement implements \JsonSerializable
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
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 */
	private $juge;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
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
     * Get idObjet
     *
     * @return int
     */
	public function getIdObjet()
    {
	    return $this->idObjet;
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
     * Get typeObjet
     *
     * @return TypeObjet
     */
	public function getTypeObjet()
    {
	    return $this->typeObjet;
    }

    /**
     * Set typeObjet
     *
     * @param TypeObjet $typeObjet
     *
     * @return Jugement
     */
    public function setTypeObjet(TypeObjet $typeObjet)
    {
        $this->typeObjet = $typeObjet;

        return $this;
    }

    /**
     * Get verdict
     *
     * @return TypeVote
     */
	public function getVerdict()
    {
	    return $this->verdict;
    }

    /**
     * Set verdict
     *
     * @param TypeVote $verdict
     *
     * @return Jugement
     */
    public function setVerdict(TypeVote $verdict = null)
    {
        $this->verdict = $verdict;

        return $this;
    }

    /**
     * Get juge
     *
     * @return \UserBundle\Entity\Membre
     */
	public function getJuge()
    {
	    return $this->juge;
    }

    /**
     * Set juge
     *
     * @param \UserBundle\Entity\Membre $juge
     *
     * @return Jugement
     */
    public function setJuge(\UserBundle\Entity\Membre $juge = null)
    {
        $this->juge = $juge;

        return $this;
    }

	/**
	 * AUTRES
	 */

	function jsonSerialize()
	{
		return array(
			'id' => $this->getId(),
			'categorieJugement' => $this->getCategorieJugement()->getCategorieJugement(),
			'description' => $this->getDescription(),
			'dateCreation' => $this->getDateCreation()->getTimestamp(),
			'auteur' => $this->getAuteur()->getPseudo(),
			'auteur_id' => $this->getAuteur()->getId(),
		);
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
	 * Get categorieJugement
	 *
	 * @return CategorieJugement
	 */
	public function getCategorieJugement()
	{
		return $this->categorieJugement;
	}

	/**
	 * Set categorieJugement
	 *
	 * @param CategorieJugement $categorieJugement
	 *
	 * @return Jugement
	 */
	public function setCategorieJugement(CategorieJugement $categorieJugement)
	{
		$this->categorieJugement = $categorieJugement;

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
	 * @return Jugement
	 */
	public function setDateCreation($dateCreation)
	{
		$this->dateCreation = $dateCreation;

		return $this;
	}

	/**
	 * Get auteur
     *
     * @return \UserBundle\Entity\Membre
     */
	public function getAuteur()
    {
	    return $this->auteur;
    }

	/**
	 * Set auteur
	 *
	 * @param \UserBundle\Entity\Membre $auteur
	 *
	 * @return Jugement
	 */
	public function setAuteur(\UserBundle\Entity\Membre $auteur)
	{
		$this->auteur = $auteur;

		return $this;
    }
}
