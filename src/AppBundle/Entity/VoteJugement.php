<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteJugement
 *
 * @ORM\Table(name="vote_jugement", indexes={
 *     @ORM\Index(name="IDX_VOTEJUGEMENT_DATECREATION", columns={"date_creation"}),
 *     @ORM\Index(name="IDX_VOTEJUGEMENT_DATEMODIFICATION", columns={"date_modification"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteJugementRepository")
 */
class VoteJugement
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @ORM\ManyToOne(targetEntity="Jugement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $jugement;

    /**
     * @ORM\ManyToOne(targetEntity="TypeVote")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vote;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
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
     * @return VoteJugement
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

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
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return VoteJugement
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get jugement
     *
     * @return Jugement
     */
	public function getJugement()
    {
	    return $this->jugement;
    }

    /**
     * Set jugement
     *
     * @param Jugement $jugement
     *
     * @return VoteJugement
     */
    public function setJugement(Jugement $jugement)
    {
        $this->jugement = $jugement;

        return $this;
    }

    /**
     * Get vote
     *
     * @return TypeVote
     */
	public function getVote()
    {
	    return $this->vote;
    }

    /**
     * Set vote
     *
     * @param TypeVote $vote
     *
     * @return VoteJugement
     */
    public function setVote(\TypeVote $vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \AppBundle\Entity\Membre
     */
	public function getAuteur()
    {
	    return $this->auteur;
    }

    /**
     * Set auteur
     *
     * @param \AppBundle\Entity\Membre $auteur
     *
     * @return VoteJugement
     */
    public function setAuteur(\AppBundle\Entity\Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }
}
