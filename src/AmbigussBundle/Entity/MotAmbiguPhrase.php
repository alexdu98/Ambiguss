<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotAmbiguPhrase
 *
 * @ORM\Table(name="mot_ambigu_phrase")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\MotAmbiguPhraseRepository")
 */
class MotAmbiguPhrase
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
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;

	/**
	 * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\Phrase")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phrase;

	/**
	 * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\MotAmbigu")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $motAmbigu;


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
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return MotAmbiguPhrase
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set phrase
     *
     * @param \AmbigussBundle\Entity\Phrase $phrase
     *
     * @return MotAmbiguPhrase
     */
    public function setPhrase(\AmbigussBundle\Entity\Phrase $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get phrase
     *
     * @return \AmbigussBundle\Entity\Phrase
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * Set motAmbigu
     *
     * @param \AmbigussBundle\Entity\MotAmbigu $motAmbigu
     *
     * @return MotAmbiguPhrase
     */
    public function setMotAmbigu(\AmbigussBundle\Entity\MotAmbigu $motAmbigu)
    {
        $this->motAmbigu = $motAmbigu;

        return $this;
    }

    /**
     * Get motAmbigu
     *
     * @return \AmbigussBundle\Entity\MotAmbigu
     */
    public function getMotAmbigu()
    {
        return $this->motAmbigu;
    }
}
