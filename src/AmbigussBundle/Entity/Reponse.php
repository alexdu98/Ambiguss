<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse", indexes={
 *     @ORM\Index(name="IDX_REPONSE_IP", columns={"ip"})
 * })
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\ReponseRepository")
 */
class Reponse
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_reponse", type="datetime")
	 */
	private $dateReponse;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu_phrase", type="string", length=1024)
     */
    private $contenuPhrase;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur_mot_ambigu", type="string", length=32)
     */
    private $valeurMotAmbigu;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur_glose", type="string", length=32)
     */
    private $valeurGlose;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="PoidsReponse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poidsReponse;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\Glose")
     * @ORM\JoinColumn(nullable=false)
     */
    private $glose;

	/**
	 * @ORM\ManyToOne(targetEntity="AmbigussBundle\Entity\MotAmbiguPhrase", inversedBy="reponses")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $motAmbiguPhrase;


    /**
     * Constructor
     */
    public function __construct()
    {
	    $this->ip = $_SERVER["REMOTE_ADDR"];
        $this->dateReponse = new \DateTime();
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
     * @return Reponse
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
	 * Set dateReponse
	 *
	 * @param \DateTime $dateReponse
	 *
	 * @return Reponse
	 */
	public function setDateReponse($dateReponse)
	{
		$this->dateReponse = $dateReponse;

		return $this;
	}

	/**
	 * Get dateReponse
	 *
	 * @return \DateTime
	 */
	public function getDateReponse()
	{
		return $this->dateReponse;
	}

    /**
     * Set contenuPhrase
     *
     * @param string $contenuPhrase
     *
     * @return Reponse
     */
    public function setContenuPhrase($contenuPhrase)
    {
        $this->contenuPhrase = $contenuPhrase;

        return $this;
    }

    /**
     * Get contenuPhrase
     *
     * @return string
     */
    public function getContenuPhrase()
    {
        return $this->contenuPhrase;
    }

    /**
     * Set valeurMotAmbigu
     *
     * @param string $valeurMotAmbigu
     *
     * @return Reponse
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
     * Set valeurGlose
     *
     * @param string $valeurGlose
     *
     * @return Reponse
     */
    public function setValeurGlose($valeurGlose)
    {
        $this->valeurGlose = $valeurGlose;

        return $this;
    }

    /**
     * Get valeurGlose
     *
     * @return string
     */
    public function getValeurGlose()
    {
        return $this->valeurGlose;
    }

    /**
     * Set auteur
     *
     * @param \UserBundle\Entity\Membre $auteur
     *
     * @return Reponse
     */
    public function setAuteur(\UserBundle\Entity\Membre $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \UserBundle\Entity\Membre
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set poidsReponse
     *
     * @param \AmbigussBundle\Entity\PoidsReponse $poidsReponse
     *
     * @return Reponse
     */
    public function setPoidsReponse(\AmbigussBundle\Entity\PoidsReponse $poidsReponse)
    {
        $this->poidsReponse = $poidsReponse;

        return $this;
    }

    /**
     * Get poidsReponse
     *
     * @return \AmbigussBundle\Entity\PoidsReponse
     */
    public function getPoidsReponse()
    {
        return $this->poidsReponse;
    }

    /**
     * Set niveau
     *
     * @param \UserBundle\Entity\Niveau $niveau
     *
     * @return Reponse
     */
    public function setNiveau(\UserBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return \UserBundle\Entity\Niveau
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set glose
     *
     * @param \AmbigussBundle\Entity\Glose $glose
     *
     * @return Reponse
     */
    public function setGlose(\AmbigussBundle\Entity\Glose $glose)
    {
        $this->glose = $glose;

        return $this;
    }

    /**
     * Get glose
     *
     * @return \AmbigussBundle\Entity\Glose
     */
    public function getGlose()
    {
        return $this->glose;
    }

    /**
     * Set motAmbiguPhrase
     *
     * @param \AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase
     *
     * @return Reponse
     */
    public function setMotAmbiguPhrase(\AmbigussBundle\Entity\MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motAmbiguPhrase = $motAmbiguPhrase;

        return $this;
    }

    /**
     * Get motAmbiguPhrase
     *
     * @return \AmbigussBundle\Entity\MotAmbiguPhrase
     */
    public function getMotAmbiguPhrase()
    {
        return $this->motAmbiguPhrase;
    }
}
