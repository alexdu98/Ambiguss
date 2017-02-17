<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mot_ambigu_phrase
 *
 * @ORM\Table(name="mot_ambigu_phrase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Mot_ambigu_phraseRepository")
 */
class Mot_ambigu_phrase
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
     * @ORM\Column(name="valeur_mot_ambigu", type="string", length=32)
     */
    private $valeurMotAmbigu;

    /**
     * @var int
     *
     * @ORM\Column(name="id_mot_ambigu", type="integer", nullable=true)
     */
    private $idMotAmbigu;

    /**
     * @var int
     *
     * @ORM\Column(name="id_phrase", type="integer", nullable=true)
     */
    private $idPhrase;

    /**
     * @ORM\ManyToOne(targetEntity="Mot_ambigu", inversedBy="mots_amibus_phrases")
     * @ORM\JoinColumn(name="id_mot_ambigu", referencedColumnName="id")
     */
    private $motAmbigu;

    /**
     * @ORM\OneToMany(targetEntity="Reponse", mappedBy="mot_ambigu_phrase")
     */
    private $Reponses;


    /**
     * @ORM\ManyToOne(targetEntity="Phrase", inversedBy="mots_ambigus_phrases")
     * @ORM\JoinColumn(name="id_phrase", referencedColumnName="id")
     */
    private $phrase;



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
     * Set valeurMotAmbigu
     *
     * @param string $valeurMotAmbigu
     *
     * @return Mot_ambigu_phrase
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
     * Set idMotAmbigu
     *
     * @param integer $idMotAmbigu
     *
     * @return Mot_ambigu_phrase
     */
    public function setIdMotAmbigu($idMotAmbigu)
    {
        $this->idMotAmbigu = $idMotAmbigu;

        return $this;
    }

    /**
     * Get idMotAmbigu
     *
     * @return int
     */
    public function getIdMotAmbigu()
    {
        return $this->idMotAmbigu;
    }

    /**
     * Set idPhrase
     *
     * @param integer $idPhrase
     *
     * @return Mot_ambigu_phrase
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
     * @return mixed
     */
    public function getMotAmbigu()
    {
        return $this->motAmbigu;
    }

    /**
     * @param mixed $motAmbigu
     */
    public function setMotAmbigu($motAmbigu)
    {
        $this->motAmbigu = $motAmbigu;
    }

    /**
     * @return mixed
     */
    public function getReponses()
    {
        return $this->Reponses;
    }

    /**
     * @param mixed $Reponses
     */
    public function setReponses($Reponses)
    {
        $this->Reponses = $Reponses;
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
     * Constructor
     */
    public function __construct()
    {
        $this->Reponses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     *
     * @return Mot_ambigu_phrase
     */
    public function addReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->Reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     */
    public function removeReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->Reponses->removeElement($reponse);
    }
}
