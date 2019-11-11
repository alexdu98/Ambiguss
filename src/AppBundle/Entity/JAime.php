<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JAime
 *
 * @ORM\Table(
 *     name="j_aime",
 *     indexes={
 *         @ORM\Index(name="ix_jaim_mbreid", columns={"membre_id"}),
 *         @ORM\Index(name="ix_jaim_phraseid", columns={"phrase_id"}),
 *         @ORM\Index(name="ix_jaim_dtcreat", columns={"date_creation"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_jaim_mbreidphraseid", columns={"membre_id", "phrase_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JAimeRepository")
 */
class JAime
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
	 * @ORM\ManyToOne(targetEntity="Phrase", inversedBy="jAime")
	 * @ORM\JoinColumn(nullable=false)
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
     * @return JAime
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
	 * @return JAime
	 */
	public function setActive($active)
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * Get membre
	 *
	 * @return Membre
	 */
	public function getMembre()
	{
		return $this->membre;
	}

    /**
     * Set membre
     *
     * @param Membre $membre
     *
     * @return JAime
     */
    public function setMembre(Membre $membre)
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
     * @return JAime
     */
    public function setPhrase(Phrase $phrase)
    {
        $this->phrase = $phrase;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->membre;
    }

}
