<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse")
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
     * @var string
     *
     * @ORM\Column(name="contenu_phrase", type="string", length=512)
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
     * @ORM\ManyToOne(targetEntity="Membre")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="Phrase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $phrase;

    /**
     * @ORM\ManyToOne(targetEntity="MotAmbiguPhrase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $motAmbiguPhrase;

    /**
     * @ORM\ManyToOne(targetEntity="PoidsReponse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poidsReponse;

    /**
     * @ORM\ManyToOne(targetEntity="Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity="Glose")
     * @ORM\JoinColumn(nullable=false)
     */
    private $glose;


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
     * @param \AmbigussBundle\Entity\Membre $auteur
     *
     * @return Reponse
     */
    public function setAuteur(\AmbigussBundle\Entity\Membre $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set phrase
     *
     * @param \AmbigussBundle\Entity\Phrase $phrase
     *
     * @return Reponse
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
     * @param \AmbigussBundle\Entity\Niveau $niveau
     *
     * @return Reponse
     */
    public function setNiveau(\AmbigussBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return \AmbigussBundle\Entity\Niveau
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
}
