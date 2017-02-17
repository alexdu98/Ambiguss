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
     * @var string
     *
     * @ORM\Column(name="valeur_mot_ambigu", type="string", length=32)
     */
    private $valeurMotAmbigu;

    /**
     * @ORM\ManyToOne(targetEntity="MotAmbigu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $motAmbigu;

	/**
	 * @ORM\ManyToOne(targetEntity="Phrase")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phrase;


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
     * Set valeurMotAmbigu
     *
     * @param string $valeurMotAmbigu
     *
     * @return MotAmbiguPhrase
     */
    public function setValeurMotAmbigu($valeurMotAmbigu)
    {
        $this->valeurMotAmbigu = $valeurMotAmbigu;

        return $this;
    }

    /**
     * Get valeurMotAmbigu
     *
     * @return string
     */
    public function getValeurMotAmbigu()
    {
        return $this->valeurMotAmbigu;
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
}
