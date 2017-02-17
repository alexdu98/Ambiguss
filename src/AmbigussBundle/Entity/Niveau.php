<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Niveau
 *
 * @ORM\Table(name="niveau")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\NiveauRepository")
 */
class Niveau
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
     * @ORM\Column(name="titre", type="string", length=32, unique=true)
     */
    private $titre;

    /**
     * @var int
     *
     * @ORM\Column(name="points_classement_min", type="integer")
     */
    private $pointsClassementMin;


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
     * Set titre
     *
     * @param string $titre
     *
     * @return Niveau
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set pointsClassementMin
     *
     * @param integer $pointsClassementMin
     *
     * @return Niveau
     */
    public function setPointsClassementMin($pointsClassementMin)
    {
        $this->pointsClassementMin = $pointsClassementMin;

        return $this;
    }

    /**
     * Get pointsClassementMin
     *
     * @return int
     */
    public function getPointsClassementMin()
    {
        return $this->pointsClassementMin;
    }
}

