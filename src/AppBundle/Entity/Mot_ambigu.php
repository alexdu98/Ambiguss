<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mot_ambigu
 *
 * @ORM\Table(name="mot_ambigu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Mot_ambiguRepository")
 */
class Mot_ambigu
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
     * @ORM\Column(name="valeur", type="string", length=32)
     */
    private $valeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime")
     */
    private $dateModification;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;


    /**
     * @ORM\OneToMany(targetEntity="Mot_ambigu_special", mappedBy="mot_ambigu")
     */
    private $motsAmbigusSpecials;

    /**
     * @ORM\OneToMany(targetEntity="Mot_ambigu_phrase", mappedBy="mot_ambigu")
     */
    private $motsAmbigusPhrases;

    /**
     * @ORM\OneToMany(targetEntity="Glose_mot_ambigu", mappedBy="mot_ambigu")
     */
    private $GloseMotsAmbigus;




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
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Mot_ambigu
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Mot_ambigu
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
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
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Mot_ambigu
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Mot_ambigu
     */
    public function setActive($active)
    {
        $this->active = $active;

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
     * @return mixed
     */
    public function getMotsAmbigusSpecials()
    {
        return $this->motsAmbigusSpecials;
    }

    /**
     * @param mixed $motsAmbigusSpecials
     */
    public function setMotsAmbigusSpecials($motsAmbigusSpecials)
    {
        $this->motsAmbigusSpecials = $motsAmbigusSpecials;
    }

    /**
     * @return mixed
     */
    public function getMotsAmbigusPhrases()
    {
        return $this->motsAmbigusPhrases;
    }

    /**
     * @param mixed $motsAmbigusPhrases
     */
    public function setMotsAmbigusPhrases($motsAmbigusPhrases)
    {
        $this->motsAmbigusPhrases = $motsAmbigusPhrases;
    }

    /**
     * @return mixed
     */
    public function getGloseMotsAmbigus()
    {
        return $this->GloseMotsAmbigus;
    }

    /**
     * @param mixed $GloseMotsAmbigus
     */
    public function setGloseMotsAmbigus($GloseMotsAmbigus)
    {
        $this->GloseMotsAmbigus = $GloseMotsAmbigus;
    }



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->motsAmbigusSpecials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->motsAmbigusPhrases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->GloseMotsAmbigus = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add motsAmbigusSpecial
     *
     * @param \AppBundle\Entity\Mot_ambigu_special $motsAmbigusSpecial
     *
     * @return Mot_ambigu
     */
    public function addMotsAmbigusSpecial(\AppBundle\Entity\Mot_ambigu_special $motsAmbigusSpecial)
    {
        $this->motsAmbigusSpecials[] = $motsAmbigusSpecial;

        return $this;
    }

    /**
     * Remove motsAmbigusSpecial
     *
     * @param \AppBundle\Entity\Mot_ambigu_special $motsAmbigusSpecial
     */
    public function removeMotsAmbigusSpecial(\AppBundle\Entity\Mot_ambigu_special $motsAmbigusSpecial)
    {
        $this->motsAmbigusSpecials->removeElement($motsAmbigusSpecial);
    }

    /**
     * Add motsAmbigusPhrase
     *
     * @param \AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase
     *
     * @return Mot_ambigu
     */
    public function addMotsAmbigusPhrase(\AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrases[] = $motsAmbigusPhrase;

        return $this;
    }

    /**
     * Remove motsAmbigusPhrase
     *
     * @param \AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase
     */
    public function removeMotsAmbigusPhrase(\AppBundle\Entity\Mot_ambigu_phrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrases->removeElement($motsAmbigusPhrase);
    }

    /**
     * Add gloseMotsAmbigus
     *
     * @param \AppBundle\Entity\Glose_mot_ambigu $gloseMotsAmbigus
     *
     * @return Mot_ambigu
     */
    public function addGloseMotsAmbigus(\AppBundle\Entity\Glose_mot_ambigu $gloseMotsAmbigus)
    {
        $this->GloseMotsAmbigus[] = $gloseMotsAmbigus;

        return $this;
    }

    /**
     * Remove gloseMotsAmbigus
     *
     * @param \AppBundle\Entity\Glose_mot_ambigu $gloseMotsAmbigus
     */
    public function removeGloseMotsAmbigus(\AppBundle\Entity\Glose_mot_ambigu $gloseMotsAmbigus)
    {
        $this->GloseMotsAmbigus->removeElement($gloseMotsAmbigus);
    }
}
