<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enum_type_succes
 *
 * @ORM\Table(name="enum_type_succes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Enum_type_succesRepository")
 */
class Enum_type_succes
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
     * @ORM\Column(name="type_succes", type="string", length=16, unique=true)
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
     * Set typeSucces
     *
     * @param string $typeSucces
     *
     * @return Enum_type_succes
     */
    public function setTypeSucces($typeSucces)
    {
        $this->typeSucces = $typeSucces;

        return $this;
    }

    /**
     * Get typeSucces
     *
     * @return string
     */
    public function getTypeSucces()
    {
        return $this->typeSucces;
    }
}
