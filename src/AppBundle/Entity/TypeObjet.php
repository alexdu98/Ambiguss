<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeObjet
 *
 * @ORM\Table(
 *     name="type_objet",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_typobj_typobj", columns={"type_objet"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeObjetRepository")
 */
class TypeObjet
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
     * @ORM\Column(name="type_objet", type="string", length=16, unique=true)
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
     * @return TypeObjet
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
