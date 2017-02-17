<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enum_poids_reponse
 *
 * @ORM\Table(name="enum_poids_reponse")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Enum_poids_reponseRepository")
 */
class Enum_poids_reponse
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
     * @var int
     *
     * @ORM\Column(name="poids_reponse", type="smallint", unique=true)
     */
    private $poidsReponse;


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
     * Set poidsReponse
     *
     * @param integer $poidsReponse
     *
     * @return Enum_poids_reponse
     */
    public function setPoidsReponse($poidsReponse)
    {
        $this->poidsReponse = $poidsReponse;

        return $this;
    }

    /**
     * Get poidsReponse
     *
     * @return int
     */
    public function getPoidsReponse()
    {
        return $this->poidsReponse;
    }
}

