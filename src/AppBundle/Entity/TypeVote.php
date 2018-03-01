<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypeVote
 *
 * @ORM\Table(name="type_vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeVoteRepository")
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
     * @ORM\Column(name="type_vote", type="string", length=16, unique=true)
     */
    private $typeVote;


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
     * Set typeVote
     *
     * @param string $typeVote
     *
     * @return TypeVote
     */
    public function setTypeVote($typeVote)
    {
        $this->typeVote = $typeVote;

        return $this;
    }

    /**
     * Get typeVote
     *
     * @return string
     */
    public function getTypeVote()
    {
        return $this->typeVote;
    }
}

