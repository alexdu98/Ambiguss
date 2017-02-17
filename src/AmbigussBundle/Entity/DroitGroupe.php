<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DroitGroupe
 *
 * @ORM\Table(name="droit_groupe")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\DroitGroupeRepository")
 */
class DroitGroupe
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
     * @ORM\ManyToOne(targetEntity="TypeDroit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeDroit;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;


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
     * Set cible
     *
     * @param string $cible
     *
     * @return DroitGroupe
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
     * @return DroitGroupe
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
}

