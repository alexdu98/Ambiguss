<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuccesMembre
 *
 * @ORM\Table(name="succes_membre")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\SuccesMembreRepository")
 */
class SuccesMembre
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
     * @ORM\ManyToOne(targetEntity="Membre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="Succes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $succes;


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
     * @return SuccesMembre
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
     * Set membre
     *
     * @param \AmbigussBundle\Entity\Membre $membre
     *
     * @return SuccesMembre
     */
    public function setMembre(\AmbigussBundle\Entity\Membre $membre)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set succes
     *
     * @param \AmbigussBundle\Entity\Succes $succes
     *
     * @return SuccesMembre
     */
    public function setSucces(\AmbigussBundle\Entity\Succes $succes)
    {
        $this->succes = $succes;

        return $this;
    }

    /**
     * Get succes
     *
     * @return \AmbigussBundle\Entity\Succes
     */
    public function getSucces()
    {
        return $this->succes;
    }
}
