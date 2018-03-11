<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeSucces
 *
 * @ORM\Table(name="type_succes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeSuccesRepository")
 */
class TypeSucces
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
     * @return TypeSucces
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

