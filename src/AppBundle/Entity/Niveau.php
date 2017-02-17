<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Niveau
 *
 * @ORM\Table(name="niveau")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NiveauRepository")
 */
class Niveau
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
     * @ORM\Column(name="label_niveau", type="string", length=32)
     */
    private $titre;

    /**
     * @var int
     *
     * @ORM\Column(name="points_classement_min", type="integer")
     */
    private $pointsClassementMin;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_jours_inscription", type="integer")
     */
    private $nbJoursInscription;

    /**
     * @ORM\OneToMany(targetEntity="Membre", mappedBy="niveau")
     */
    private $membres;

    

    public function __construct()
    {
        $this->membres = new ArrayCollection();
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Niveau
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set pointsClassementMin
     *
     * @param integer $pointsClassementMin
     *
     * @return Niveau
     */
    public function setPointsClassementMin($pointsClassementMin)
    {
        $this->pointsClassementMin = $pointsClassementMin;

        return $this;
    }

    /**
     * Get pointsClassementMin
     *
     * @return int
     */
    public function getPointsClassementMin()
    {
        return $this->pointsClassementMin;
    }

    /**
     * Set nbJoursInscription
     *
     * @param integer $nbJoursInscription
     *
     * @return Niveau
     */
    public function setNbJoursInscription($nbJoursInscription)
    {
        $this->nbJoursInscription = $nbJoursInscription;

        return $this;
    }

    /**
     * Get nbJoursInscription
     *
     * @return int
     */
    public function getNbJoursInscription()
    {
        return $this->nbJoursInscription;
    }

    /**
     * @return mixed
     */
    public function getMembres()
    {
        return $this->membres;
    }

    /**
     * @param mixed $membres
     */
    public function setMembres($membres)
    {
        $this->membres = $membres;
    }



    /**
     * Add membre
     *
     * @param \AppBundle\Entity\Membre $membre
     *
     * @return Niveau
     */
    public function addMembre(\AppBundle\Entity\Membre $membre)
    {
        $this->membres[] = $membre;

        return $this;
    }

    /**
     * Remove membre
     *
     * @param \AppBundle\Entity\Membre $membre
     */
    public function removeMembre(\AppBundle\Entity\Membre $membre)
    {
        $this->membres->removeElement($membre);
    }
}
