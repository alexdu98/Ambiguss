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
	 * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\Phrase", inversedBy="motsAmbigusPhrase")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	private $phrase;

	/**
	 * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\MotAmbigu", cascade={"persist"})
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $motAmbigu;

	/**
	 * @ORM\OneToMany(targetEntity="AmbigussBundle\Entity\Reponse", mappedBy="motAmbiguPhrase")
	 */
	private $reponses;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->reponses = new \Doctrine\Common\Collections\ArrayCollection();
	}

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
	 * Get ordre
	 *
	 * @return int
	 */
	public function getOrdre()
	{
		return $this->ordre;
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
     * Get phrase
     *
     * @return \AmbigussBundle\Entity\Phrase
     */
	public function getPhrase()
    {
	    return $this->phrase;
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
     * Get motAmbigu
     *
     * @return \AmbigussBundle\Entity\MotAmbigu
     */
	public function getMotAmbigu()
    {
	    return $this->motAmbigu;
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
     * Add reponse
     *
     * @param \AmbigussBundle\Entity\Reponse $reponse
     *
     * @return MotAmbiguPhrase
     */
    public function addReponse(\AmbigussBundle\Entity\Reponse $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \AmbigussBundle\Entity\Reponse $reponse
     */
    public function removeReponse(\AmbigussBundle\Entity\Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponses()
    {
        return $this->reponses;
    }
}
