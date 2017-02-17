<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enum_categorie_jugement
 *
 * @ORM\Table(name="enum_categorie_jugement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Enum_categorie_jugementRepository")
 */
class Enum_categorie_jugement
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
     * @return Enum_categorie_jugement
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

