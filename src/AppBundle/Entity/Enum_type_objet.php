<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enum_type_objet
 *
 * @ORM\Table(name="enum_type_objet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Enum_type_objetRepository")
 */
class Enum_type_objet
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
     * @return Enum_type_objet
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

