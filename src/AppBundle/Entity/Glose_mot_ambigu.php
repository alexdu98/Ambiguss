<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Glose_mot_ambigu
 *
 * @ORM\Table(name="glose_mot_ambigu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Glose_mot_ambiguRepository")
 */
class Glose_mot_ambigu
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
     * @ORM\Column(name="id_glose", type="integer", nullable=true)
     */
    private $idGlose;

    /**
     * @var int
     *
     * @ORM\Column(name="id_mot_ambigu", type="integer", nullable=true)
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
     * @return int
     */
    public function getIdGlose()
    {
        return $this->idGlose;
    }

    /**
     * @param int $idGlose
     */
    public function setIdGlose($idGlose)
    {
        $this->idGlose = $idGlose;
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


}
