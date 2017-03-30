<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SuccesMembre
 *
 * @ORM\Table(name="succes_membre", indexes={
 *     @ORM\Index(name="IDX_SUCCESMEMBRE_DATECREATION", columns={"date_creation"})
 * })
 * @ORM\Entity(repositoryClass="UserBundle\Repository\SuccesMembreRepository")
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
     * @param \UserBundle\Entity\Membre $membre
     *
     * @return SuccesMembre
     */
    public function setMembre(\UserBundle\Entity\Membre $membre)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return \UserBundle\Entity\Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set succes
     *
     * @param \UserBundle\Entity\Succes $succes
     *
     * @return SuccesMembre
     */
    public function setSucces(\UserBundle\Entity\Succes $succes)
    {
        $this->succes = $succes;

        return $this;
    }

    /**
     * Get succes
     *
     * @return \UserBundle\Entity\Succes
     */
    public function getSucces()
    {
        return $this->succes;
    }
}
