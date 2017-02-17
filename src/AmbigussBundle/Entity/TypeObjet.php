<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeObjet
 *
 * @ORM\Table(name="type_objet")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\TypeObjetRepository")
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
    private $typeObjet;


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
     * Set typeObjet
     *
     * @param string $typeObjet
     *
     * @return TypeObjet
     */
    public function setTypeObjet($typeObjet)
    {
        $this->typeObjet = $typeObjet;

        return $this;
    }

    /**
     * Get typeObjet
     *
     * @return string
     */
    public function getTypeObjet()
    {
        return $this->typeObjet;
    }
}

