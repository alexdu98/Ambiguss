<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Succes
 *
 * @ORM\Table(name="succes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SuccesRepository")
 */
class Succes
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
     * @ORM\Column(name="titre", type="string", length=64)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="conditions", type="string", length=256)
     */
    private $conditions;

    /**
     * @var int
     *
     * @ORM\Column(name="recompense", type="integer")
     */
    private $recompense;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=256)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="id_succes_parent", type="integer", nullable=true)
     */
    private $idSuccesParent;
    /**
     * @ORM\OneToMany(targetEntity="Succes_membre", mappedBy="succes")
     */
    private $succesMembres;

    /**
     * @ORM\ManyToOne(targetEntity="Enum_type_succes")
     * @ORM\JoinColumn(name="id_enum_type_succes", referencedColumnName="id", nullable=false)
     */
    private $typeSucces;

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
     * @return Succes
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
     * Set description
     *
     * @param string $description
     *
     * @return Succes
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set conditions
     *
     * @param string $conditions
     *
     * @return Succes
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions
     *
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set recompense
     *
     * @param integer $recompense
     *
     * @return Succes
     */
    public function setRecompense($recompense)
    {
        $this->recompense = $recompense;

        return $this;
    }

    /**
     * Get recompense
     *
     * @return int
     */
    public function getRecompense()
    {
        return $this->recompense;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Succes
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set idSuccesParent
     *
     * @param integer $idSuccesParent
     *
     * @return Succes
     */
    public function setIdSuccesParent($idSuccesParent)
    {
        $this->idSuccesParent = $idSuccesParent;

        return $this;
    }

    /**
     * Get idSuccesParent
     *
     * @return int
     */
    public function getIdSuccesParent()
    {
        return $this->idSuccesParent;
    }

    /**
     * @return mixed
     */
    public function getSuccesMembres()
    {
        return $this->succesMembres;
    }

    /**
     * @param mixed $succesMembres
     */
    public function setSuccesMembres($succesMembres)
    {
        $this->succesMembres = $succesMembres;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->succesMembres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add succesMembre
     *
     * @param \AppBundle\Entity\Succes_membre $succesMembre
     *
     * @return Succes
     */
    public function addSuccesMembre(\AppBundle\Entity\Succes_membre $succesMembre)
    {
        $this->succesMembres[] = $succesMembre;

        return $this;
    }

    /**
     * Remove succesMembre
     *
     * @param \AppBundle\Entity\Succes_membre $succesMembre
     */
    public function removeSuccesMembre(\AppBundle\Entity\Succes_membre $succesMembre)
    {
        $this->succesMembres->removeElement($succesMembre);
    }

    /**
     * Set typeSucces
     *
     * @param \AppBundle\Entity\Enum_type_succes $typeSucces
     *
     * @return Succes
     */
    public function setTypeSucces(\AppBundle\Entity\Enum_type_succes $typeSucces)
    {
        $this->typeSucces = $typeSucces;

        return $this;
    }

    /**
     * Get typeSucces
     *
     * @return \AppBundle\Entity\Enum_type_succes
     */
    public function getTypeSucces()
    {
        return $this->typeSucces;
    }
}
