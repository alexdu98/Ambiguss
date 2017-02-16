<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mot_ambigu_special
 *
 * @ORM\Table(name="mot_ambigu_special")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Mot_ambigu_specialRepository")
 */
class Mot_ambigu_special
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
     * @ORM\Column(name="valeur", type="string", length=32)
     */
    private $valeur;


    /**
     * @var bool
     *
     * @ORM\Column(name="ambigu", type="boolean")
     */
    private $ambigu;


    /**
     * @var int
     *
     * @ORM\Column(name="id_MotAmbigu", type="integer", nullable=true)
     */
    private $idMotAmbigu;
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
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Mot_ambigu_special
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * @return boolean
     */
    public function isAmbigu()
    {
        return $this->ambigu;
    }

    /**
     * @param boolean $ambigu
     */
    public function setAmbigu($ambigu)
    {
        $this->ambigu = $ambigu;
    }

    /**
     * @return int
     */
    public function getIdMotAmbigu()
    {
        return $this->idMotAmbigu;
    }

    /**
     * @param int $idMotAmbigu
     */
    public function setIdMotAmbigu($idMotAmbigu)
    {
        $this->idMotAmbigu = $idMotAmbigu;
    }

    

    /**
     * Get ambigu
     *
     * @return boolean
     */
    public function getAmbigu()
    {
        return $this->ambigu;
    }
}
