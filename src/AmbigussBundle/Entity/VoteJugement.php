<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoteJugement
 *
 * @ORM\Table(name="vote_jugement")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\VoteJugementRepository")
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
     * @return VoteJugement
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
     * Set jugement
     *
     * @param \AmbigussBundle\Entity\Jugement $jugement
     *
     * @return VoteJugement
     */
    public function setJugement(\AmbigussBundle\Entity\Jugement $jugement)
    {
        $this->jugement = $jugement;

        return $this;
    }

    /**
     * Get jugement
     *
     * @return \AmbigussBundle\Entity\Jugement
     */
    public function getJugement()
    {
        return $this->jugement;
    }

    /**
     * Set vote
     *
     * @param \AmbigussBundle\Entity\TypeVote $vote
     *
     * @return VoteJugement
     */
    public function setVote(\AmbigussBundle\Entity\TypeVote $vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return \AmbigussBundle\Entity\TypeVote
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set auteur
     *
     * @param \AmbigussBundle\Entity\Membre $auteur
     *
     * @return VoteJugement
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
