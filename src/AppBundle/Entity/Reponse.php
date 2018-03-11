<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse", indexes={
 *     @ORM\Index(name="IDX_REPONSE_IP", columns={"ip"}),
 *     @ORM\Index(name="IDX_REPONSE_DATEREPONSE", columns={"date_reponse"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReponseRepository")
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
     * @ORM\Column(name="valeur_mot_ambigu", type="string", length=32, options={"collation":"utf8_bin"})
     */
    private $valeurMotAmbigu;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur_glose", type="string", length=32)
     */
    private $valeurGlose;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Membre")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="PoidsReponse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poidsReponse;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Glose")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $glose;

	/**
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MotAmbiguPhrase", inversedBy="reponses")
	 * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
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
     * @return Reponse
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

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
	 * Get contenuPhrase
	 *
	 * @return string
	 */
	public function getContenuPhrase()
	{
		return $this->contenuPhrase;
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
     * Get valeurMotAmbigu
     *
     * @return string
     */
	public function getValeurMotAmbigu()
    {
	    return $this->valeurMotAmbigu;
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
     * Get valeurGlose
     *
     * @return string
     */
	public function getValeurGlose()
    {
	    return $this->valeurGlose;
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
     * Get auteur
     *
     * @return \AppBundle\Entity\Membre
     */
	public function getAuteur()
    {
	    return $this->auteur;
    }

    /**
     * Set auteur
     *
     * @param \AppBundle\Entity\Membre $auteur
     *
     * @return Reponse
     */
    public function setAuteur(\AppBundle\Entity\Membre $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get poidsReponse
     *
     * @return PoidsReponse
     */
	public function getPoidsReponse()
    {
	    return $this->poidsReponse;
    }

    /**
     * Set poidsReponse
     *
     * @param PoidsReponse $poidsReponse
     *
     * @return Reponse
     */
    public function setPoidsReponse(PoidsReponse $poidsReponse)
    {
        $this->poidsReponse = $poidsReponse;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return \AppBundle\Entity\Niveau
     */
	public function getNiveau()
    {
	    return $this->niveau;
    }

    /**
     * Set niveau
     *
     * @param \AppBundle\Entity\Niveau $niveau
     *
     * @return Reponse
     */
    public function setNiveau(\AppBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get glose
     *
     * @return Glose
     */
	public function getGlose()
    {
	    return $this->glose;
    }

    /**
     * Set glose
     *
     * @param Glose $glose
     *
     * @return Reponse
     */
    public function setGlose(Glose $glose)
    {
        $this->glose = $glose;

        return $this;
    }

    /**
     * Get motAmbiguPhrase
     *
     * @return MotAmbiguPhrase
     */
	public function getMotAmbiguPhrase()
    {
	    return $this->motAmbiguPhrase;
    }

    /**
     * Set motAmbiguPhrase
     *
     * @param MotAmbiguPhrase $motAmbiguPhrase
     *
     * @return Reponse
     */
    public function setMotAmbiguPhrase(MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motAmbiguPhrase = $motAmbiguPhrase;

        return $this;
    }
}
