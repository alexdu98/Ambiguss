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
     * @ORM\ManyToOne(targetEntity="Mot_ambigu", inversedBy="glose_mots_ambigus")
     * @ORM\JoinColumn(name="id_mot_ambigu", referencedColumnName="id")
     */
    private $motAmbigu;


    /**
     * @ORM\ManyToOne(targetEntity="glose", inversedBy="glose_mots_ambigus")
     * @ORM\JoinColumn(name="id_glose", referencedColumnName="id")
     */
    private $glose;

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
    public function getGlose()
    {
        return $this->glose;
    }

    /**
     * @return mixed
     */
    public function getMotAmbigu()
    {
        return $this->motAmbigu;
    }

    /**
     * @param mixed $motAmbigu
     */
    public function setMotAmbigu($motAmbigu)
    {
        $this->motAmbigu = $motAmbigu;
    }

    /**
     * @param mixed $glose
     */
    public function setGlose($glose)
    {
        $this->glose = $glose;
    }


}
