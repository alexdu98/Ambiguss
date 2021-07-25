<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeVote
 *
 * @ORM\Table(
 *     name="type_vote",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_typvot_typvot", columns={"nom"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TypeVoteRepository")
 */
class TypeVote
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
     * @return TypeVote
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
