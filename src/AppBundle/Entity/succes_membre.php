<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * succes_membre
 *
 * @ORM\Table(name="succes_membre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\succes_membreRepository")
 */
class Succes_membre
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
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="succes_membres")
     * @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="Succes", inversedBy="succes_membres")
     * @ORM\JoinColumn(name="id_succes", referencedColumnName="id")
     */
    private $succes;




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
     * @return succes_membre
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
    public function getSucces()
    {
        return $this->succes;
    }

    /**
     * @param mixed $succes
     */
    public function setSucces($succes)
    {
        $this->succes = $succes;
    }


}
