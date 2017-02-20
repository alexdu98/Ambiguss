<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeDroit
 *
 * @ORM\Table(name="type_droit")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\TypeDroitRepository")
 */
class TypeDroit
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
     * @ORM\Column(name="type_droit", type="string", length=8, unique=true)
     */
    private $typeDroit;


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
     * Set typeDroit
     *
     * @param string $typeDroit
     *
     * @return TypeDroit
     */
    public function setTypeDroit($typeDroit)
    {
        $this->typeDroit = $typeDroit;

        return $this;
    }

    /**
     * Get typeDroit
     *
     * @return string
     */
    public function getTypeDroit()
    {
        return $this->typeDroit;
    }
}

