<?php

namespace AppBundle\Entity;

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
    private $labelNiveau;

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
     * Set labelNiveau
     *
     * @param string $labelNiveau
     *
     * @return Niveau
     */
    public function setLabelNiveau($labelNiveau)
    {
        $this->labelNiveau = $labelNiveau;

        return $this;
    }

    /**
     * Get labelNiveau
     *
     * @return string
     */
    public function getLabelNiveau()
    {
        return $this->labelNiveau;
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


}

