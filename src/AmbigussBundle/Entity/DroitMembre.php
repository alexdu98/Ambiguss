<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DroitMembre
 *
 * @ORM\Table(name="droit_membre")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\DroitMembreRepository")
 */
class DroitMembre
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
	 * @ORM\ManyToOne(targetEntity="Membre")
	 * @ORM\JoinColumn(nullable=false)
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
     * Set cible
     *
     * @param string $cible
     *
     * @return DroitMembre
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
     * @return DroitMembre
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
     * Set typeDroit
     *
     * @param \AmbigussBundle\Entity\TypeDroit $typeDroit
     *
     * @return DroitMembre
     */
    public function setTypeDroit(\AmbigussBundle\Entity\TypeDroit $typeDroit)
    {
        $this->typeDroit = $typeDroit;

        return $this;
    }

    /**
     * Get typeDroit
     *
     * @return \AmbigussBundle\Entity\TypeDroit
     */
    public function getTypeDroit()
    {
        return $this->typeDroit;
    }

    /**
     * Set membre
     *
     * @param \AmbigussBundle\Entity\Membre $membre
     *
     * @return DroitMembre
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
}
