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
     * @var enum
     *
     * @ORM\Column(name="type", type="string", length=256)
     */

    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="Conditions", type="string", length=256)
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}
