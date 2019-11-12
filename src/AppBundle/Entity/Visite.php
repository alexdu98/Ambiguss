<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visite
 *
 * @ORM\Table(name="visite", indexes={
 *     @ORM\Index(name="ix_visit_dtvisit", columns={"date_visite"}),
 *     @ORM\Index(name="ix_visit_ip", columns={"ip"}),
 *     @ORM\Index(name="ix_visit_useragent", columns={"user_agent"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VisiteRepository")
 */
class Visite
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
     * @ORM\Column(name="ip", type="string", length=39)
     */
    private $ip;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="user_agent", type="string", length=255, nullable=true)
	 */
	private $userAgent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_visite", type="datetime")
     */
    private $dateVisite;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateVisite = new \DateTime();
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
	 * Get ip
	 *
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Visite
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

	/**
	 * Get userAgent
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->userAgent;
	}

	/**
	 * Set userAgent
	 *
	 * @param string $userAgent
	 *
	 * @return Visite
	 */
	public function setUserAgent($userAgent)
	{
		$this->userAgent = $userAgent;

		return $this;
	}

	/**
	 * Get dateVisite
	 *
	 * @return \DateTime
	 */
	public function getDateVisite()
	{
		return $this->dateVisite;
	}

    /**
     * Set dateVisite
     *
     * @param \DateTime $dateVisite
     *
     * @return Visite
     */
    public function setDateVisite($dateVisite)
    {
        $this->dateVisite = $dateVisite;

        return $this;
    }

}
