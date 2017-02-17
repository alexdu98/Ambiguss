<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieJugement
 *
 * @ORM\Table(name="categorie_jugement")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\CategorieJugementRepository")
 */
class CategorieJugement
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
     * @ORM\Column(name="categorie_jugement", type="string", length=16, unique=true)
     */
    private $categorieJugement;


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
     * Set categorieJugement
     *
     * @param string $categorieJugement
     *
     * @return CategorieJugement
     */
    public function setCategorieJugement($categorieJugement)
    {
        $this->categorieJugement = $categorieJugement;

        return $this;
    }

    /**
     * Get categorieJugement
     *
     * @return string
     */
    public function getCategorieJugement()
    {
        return $this->categorieJugement;
    }
}

