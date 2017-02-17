<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoidsReponse
 *
 * @ORM\Table(name="poids_reponse")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\PoidsReponseRepository")
 */
class PoidsReponse
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
     * @return PoidsReponse
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

