<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieSignalement
 *
 * @ORM\Table(
 *     name="categorie_signalement",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_catsig_nom", columns={"nom"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategorieSignalementRepository")
 */
class CategorieSignalement
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
     * @ORM\Column(name="nom", type="string", length=32, unique=true)
     */
    private $nom;


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
     * @return CategorieSignalement
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

    public function __toString()
    {
        return $this->nom;
    }

}
