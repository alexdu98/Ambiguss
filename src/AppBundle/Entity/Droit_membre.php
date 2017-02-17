<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * droit_membre
 *
 * @ORM\Table(name="droit_membre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\droit_membreRepository")
 */
class Droit_membre
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
     * @ORM\Column(name="type", type="string", length=8)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="cible", type="string", length=32)
     */
    private $cible;

    /**
     * @var bool
     *
     * @ORM\Column(name="autoriser", type="boolean")
     */
    private $autoriser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_membre", type="integer", nullable=true)
     */
    private $idMembre;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="droit_membres")
     * @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
     */
    private $membre;

    


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
     * Set type
     *
     * @param string $type
     *
     * @return droit_membre
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set cible
     *
     * @param string $cible
     *
     * @return droit_membre
     */
    public function setCible($cible)
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * Get cible
     *
     * @return string
     */
    public function getCible()
    {
        return $this->cible;
    }

    /**
     * Set autoriser
     *
     * @param boolean $autoriser
     *
     * @return droit_membre
     */
    public function setAutoriser($autoriser)
    {
        $this->autoriser = $autoriser;

        return $this;
    }

    /**
     * Get autoriser
     *
     * @return bool
     */
    public function getAutoriser()
    {
        return $this->autoriser;
    }

    /**
     * Set idMembre
     *
     * @param integer $idMembre
     *
     * @return droit_membre
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;

        return $this;
    }

    /**
     * Get idMembre
     *
     * @return int
     */
    public function getIdMembre()
    {
        return $this->idMembre;
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
    
}
