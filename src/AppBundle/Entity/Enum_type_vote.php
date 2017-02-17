<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enum_type_vote
 *
 * @ORM\Table(name="enum_type_vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Enum_type_voteRepository")
 */
class Enum_type_vote
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
     * @param string $vote
     *
     * @return Enum_type_vote
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

