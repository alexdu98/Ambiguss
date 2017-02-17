<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse")
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
     * @var int
     *
     * @ORM\Column(name="nb_secondes", type="integer")
     */
    private $nbSecondes;

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
     * @var int
     *
     * @ORM\Column(name="id_auteur", type="integer", nullable=true)
     */
    private $idAuteur;

    /**
     * @var int
     *
     * @ORM\Column(name="id_phrase", type="integer", nullable=true)
     */
    private $idPhrase;

    /**
     * @var int
     *
     * @ORM\Column(name="id_mot_ambigu_phrase", type="integer", nullable=true)
     */
    private $idMotAmbiguPhrase;

    /**
     * @var int
     *
     * @ORM\Column(name="id_glose", type="integer", nullable=true)
     */
    private $idGlose;

    /**
     * @ORM\ManyToOne(targetEntity="Glose", inversedBy="reponses")
     * @ORM\JoinColumn(name="id_glose", referencedColumnName="id")
     */
    private $glose;


    /**
     * @ORM\ManyToOne(targetEntity="Mot_ambigu_phrase", inversedBy="reponses")
     * @ORM\JoinColumn(name="id_mot_ambigu_phrase", referencedColumnName="id")
     */
    private $Mot_ambigu_phrase;

    /**
     * @ORM\ManyToOne(targetEntity="Phrase", inversedBy="reponses")
     * @ORM\JoinColumn(name="id_phrase", referencedColumnName="id")
     */
    private $phrase;

    /**
     * @ORM\ManyToOne(targetEntity="Enum_poids_reponse")
     * @ORM\JoinColumn(name="id_enum_poids_reponse", referencedColumnName="id", nullable=false)
     */
    private $poidsReponse;


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
     * Set nbSecondes
     *
     * @param integer $nbSecondes
     *
     * @return Reponse
     */
    public function setNbSecondes($nbSecondes)
    {
        $this->nbSecondes = $nbSecondes;

        return $this;
    }

    /**
     * Get nbSecondes
     *
     * @return int
     */
    public function getNbSecondes()
    {
        return $this->nbSecondes;
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
     * Set idAuteur
     *
     * @param integer $idAuteur
     *
     * @return Reponse
     */
    public function setIdAuteur($idAuteur)
    {
        $this->idAuteur = $idAuteur;

        return $this;
    }

    /**
     * Get idAuteur
     *
     * @return int
     */
    public function getIdAuteur()
    {
        return $this->idAuteur;
    }

    /**
     * Set idPhrase
     *
     * @param integer $idPhrase
     *
     * @return Reponse
     */
    public function setIdPhrase($idPhrase)
    {
        $this->idPhrase = $idPhrase;

        return $this;
    }

    /**
     * Get idPhrase
     *
     * @return int
     */
    public function getIdPhrase()
    {
        return $this->idPhrase;
    }

    /**
     * Set idMotAmbiguPhrase
     *
     * @param integer $idMotAmbiguPhrase
     *
     * @return Reponse
     */
    public function setIdMotAmbiguPhrase($idMotAmbiguPhrase)
    {
        $this->idMotAmbiguPhrase = $idMotAmbiguPhrase;

        return $this;
    }

    /**
     * Get idMotAmbiguPhrase
     *
     * @return int
     */
    public function getIdMotAmbiguPhrase()
    {
        return $this->idMotAmbiguPhrase;
    }

    /**
     * Set idGlose
     *
     * @param integer $idGlose
     *
     * @return Reponse
     */
    public function setIdGlose($idGlose)
    {
        $this->idGlose = $idGlose;

        return $this;
    }

    /**
     * Get idGlose
     *
     * @return int
     */
    public function getIdGlose()
    {
        return $this->idGlose;
    }

    /**
     * @return mixed
     */
    public function getGlose()
    {
        return $this->glose;
    }

    /**
     * @param mixed $glose
     */
    public function setGlose($glose)
    {
        $this->glose = $glose;
    }

    /**
     * @return mixed
     */
    public function getMotAmbiguPhrase()
    {
        return $this->Mot_ambigu_phrase;
    }

    /**
     * @param mixed $Mot_ambigu_phrase
     */
    public function setMotAmbiguPhrase($Mot_ambigu_phrase)
    {
        $this->Mot_ambigu_phrase = $Mot_ambigu_phrase;
    }

    /**
     * @return mixed
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * @param mixed $phrase
     */
    public function setPhrase($phrase)
    {
        $this->phrase = $phrase;
    }



    /**
     * Set poidsReponse
     *
     * @param \AppBundle\Entity\Enum_poids_reponse $poidsReponse
     *
     * @return Reponse
     */
    public function setPoidsReponse(\AppBundle\Entity\Enum_poids_reponse $poidsReponse)
    {
        $this->poidsReponse = $poidsReponse;

        return $this;
    }

    /**
     * Get poidsReponse
     *
     * @return \AppBundle\Entity\Enum_poids_reponse
     */
    public function getPoidsReponse()
    {
        return $this->poidsReponse;
    }
}
