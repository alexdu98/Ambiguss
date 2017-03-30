<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visite
 *
 * @ORM\Table(name="visite", indexes={
 *     @ORM\Index(name="IDX_VISITE_IP", columns={"ip"}),
 *     @ORM\Index(name="IDX_VISITE_DATEVISITE", columns={"date_visite"})
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
    	$this->ip = $_SERVER['REMOTE_ADDR'];
    	$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
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
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
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
	 * Get userAgent
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->userAgent;
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

    /**
     * Get dateVisite
     *
     * @return \DateTime
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }
}

