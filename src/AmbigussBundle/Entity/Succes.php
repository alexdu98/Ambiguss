<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Succes
 *
 * @ORM\Table(name="succes")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\SuccesRepository")
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
     * @ORM\Column(name="titre", type="string", length=64, unique=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="conditions", type="json_array")
     */
    private $conditions;

    /**
     * @var array
     *
     * @ORM\Column(name="recompense", type="json_array")
     */
    private $recompense;

    /**
     * @var string
     *
     * @ORM\Column(name="url_image", type="string", length=255, unique=true)
     */
    private $urlImage;

    /**
     * @ORM\ManyToOne(targetEntity="Succes")
     */
    private $succesParent;

	/**
	 * @ORM\ManyToOne(targetEntity="TypeSucces")
	 * @ORM\JoinColumn(nullable=false)
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
     * @param array $conditions
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
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set recompense
     *
     * @param array $recompense
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
     * @return array
     */
    public function getRecompense()
    {
        return $this->recompense;
    }

    /**
     * Set urlImage
     *
     * @param string $urlImage
     *
     * @return Succes
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get urlImage
     *
     * @return string
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }

    /**
     * Set succesParent
     *
     * @param \AmbigussBundle\Entity\Succes $succesParent
     *
     * @return Succes
     */
    public function setSuccesParent(\AmbigussBundle\Entity\Succes $succesParent = null)
    {
        $this->succesParent = $succesParent;

        return $this;
    }

    /**
     * Get succesParent
     *
     * @return \AmbigussBundle\Entity\Succes
     */
    public function getSuccesParent()
    {
        return $this->succesParent;
    }

    /**
     * Set typeSucces
     *
     * @param \AmbigussBundle\Entity\TypeSucces $typeSucces
     *
     * @return Succes
     */
    public function setTypeSucces(\AmbigussBundle\Entity\TypeSucces $typeSucces)
    {
        $this->typeSucces = $typeSucces;

        return $this;
    }

    /**
     * Get typeSucces
     *
     * @return \AmbigussBundle\Entity\TypeSucces
     */
    public function getTypeSucces()
    {
        return $this->typeSucces;
    }
}
