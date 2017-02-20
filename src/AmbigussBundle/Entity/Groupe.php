<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\GroupeRepository")
 */
class Groupe
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
     * @ORM\Column(name="nom", type="string", length=16, unique=true)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe")
     */
    private $groupeParent;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Groupe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set groupeParent
     *
     * @param \AmbigussBundle\Entity\Groupe $groupeParent
     *
     * @return Groupe
     */
    public function setGroupeParent(\AmbigussBundle\Entity\Groupe $groupeParent = null)
    {
        $this->groupeParent = $groupeParent;

        return $this;
    }

    /**
     * Get groupeParent
     *
     * @return \AmbigussBundle\Entity\Groupe
     */
    public function getGroupeParent()
    {
        return $this->groupeParent;
    }
}
