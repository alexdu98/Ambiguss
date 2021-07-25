<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MotAmbiguPhrase
 * Ne pas avoir de clé unique sur {phrase,ordre} car Doctrine fait les insert d'abord dans une transaction #editPhrase
 * @ORM\Table(
 *     name="mot_ambigu_phrase",
 *     indexes={
 *         @ORM\Index(name="fk_motambphrase_phraseid", columns={"phrase_id"}),
 *         @ORM\Index(name="fk_motambphrase_motambid", columns={"mot_ambigu_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\MotAmbiguPhraseRepository")
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
	 * @ORM\ManyToOne(targetEntity="Phrase", inversedBy="motsAmbigusPhrase")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $phrase;

	/**
	 * @ORM\ManyToOne(targetEntity="MotAmbigu", cascade={"persist"})
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $motAmbigu;

	/**
	 * @ORM\OneToMany(targetEntity="Reponse", mappedBy="motAmbiguPhrase", cascade={"remove"})
	 */
	private $reponses;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->reponses = new ArrayCollection();
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
     * @return Phrase
     */
	public function getPhrase()
    {
	    return $this->phrase;
    }

    /**
     * Set phrase
     *
     * @param Phrase $phrase
     *
     * @return MotAmbiguPhrase
     */
    public function setPhrase(Phrase $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    /**
     * Get motAmbigu
     *
     * @return MotAmbigu
     */
	public function getMotAmbigu()
    {
	    return $this->motAmbigu;
    }

    /**
     * Set motAmbigu
     *
     * @param MotAmbigu $motAmbigu
     *
     * @return MotAmbiguPhrase
     */
    public function setMotAmbigu(MotAmbigu $motAmbigu)
    {
        $this->motAmbigu = $motAmbigu;

        return $this;
    }

    /**
     * Add reponse
     *
     * @param Reponse $reponse
     *
     * @return MotAmbiguPhrase
     */
    public function addReponse(Reponse $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param Reponse $reponse
     */
    public function removeReponse(Reponse $reponse)
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

    public function __toString()
    {
        return (string) $this->phrase . ' (' . $this->motAmbigu . ': #' . $this->ordre . ')';
    }

}
