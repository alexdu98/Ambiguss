<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AimerPhrase
 *
 * @ORM\Table(name="aimer_phrase", indexes={
 *     @ORM\Index(name="IDX_AIMERPHRASE_DATECREATION", columns={"date_creation"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AimerPhraseRepository")
 */
class AimerPhrase
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
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="active", type="boolean")
	 */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

	/**
	 * @ORM\ManyToOne(targetEntity="Phrase", inversedBy="likesPhrase")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
	 */
	private $phrase;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dateCreation = new \DateTime();
		$this->active = true;
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
	 * Get dateCreation
	 *
	 * @return \DateTime
	 */
	public function getDateCreation()
	{
		return $this->dateCreation;
	}

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return AimerPhrase
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

	/**
	 * Get active
	 *
	 * @return bool
	 */
	public function getActive()
	{
		return $this->active;
	}

	/**
	 * Set active
	 *
	 * @param boolean $active
	 *
	 * @return AimerPhrase
	 */
	public function setActive($active)
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * Get membre
	 *
	 * @return \AppBundle\Entity\Membre
	 */
	public function getMembre()
	{
		return $this->membre;
	}

    /**
     * Set membre
     *
     * @param \AppBundle\Entity\Membre $membre
     *
     * @return AimerPhrase
     */
    public function setMembre(\AppBundle\Entity\Membre $membre)
    {
        $this->membre = $membre;

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
     * @return AimerPhrase
     */
    public function setPhrase(Phrase $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }
}
