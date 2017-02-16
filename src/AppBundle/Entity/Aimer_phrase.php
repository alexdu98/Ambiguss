<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Aimer_phrase
 *
 * @ORM\Table(name="aimer_phrase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Aimer_phraseRepository")
 */
class Aimer_phrase
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_aimer", type="datetime")
     */
    private $dateAimer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_desaimer", type="datetime")
     */
    private $dateDesaimer;


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
     * Set dateAimer
     *
     * @param \DateTime $dateAimer
     *
     * @return Aimer_phrase
     */
    public function setDateAimer($dateAimer)
    {
        $this->dateAimer = $dateAimer;

        return $this;
    }

    /**
     * Get dateAimer
     *
     * @return \DateTime
     */
    public function getDateAimer()
    {
        return $this->dateAimer;
    }

    /**
     * Set dateDesaimer
     *
     * @param \DateTime $dateDesaimer
     *
     * @return Aimer_phrase
     */
    public function setDateDesaimer($dateDesaimer)
    {
        $this->dateDesaimer = $dateDesaimer;

        return $this;
    }

    /**
     * Get dateDesaimer
     *
     * @return \DateTime
     */
    public function getDateDesaimer()
    {
        return $this->dateDesaimer;
    }
}
