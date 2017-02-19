<?php

namespace AmbigussBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MembreRole
 *
 * @ORM\Table(name="membre_role")
 * @ORM\Entity(repositoryClass="AmbigussBundle\Repository\MembreRoleRepository")
 */
class MembreRole
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
     * @var bool
     *
     * @ORM\Column(name="autoriser", type="boolean")
     */
    private $autoriser;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="membreRoles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;


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
     * Set autoriser
     *
     * @param boolean $autoriser
     *
     * @return MembreRole
     */
    public function setAutoriser($autoriser)
    {
        $this->autoriser = $autoriser;

        return $this;
    }

    /**
     * Get autoriser
     *
     * @return bool
     */
    public function getAutoriser()
    {
        return $this->autoriser;
    }

    /**
     * Set membre
     *
     * @param \AmbigussBundle\Entity\Membre $membre
     *
     * @return MembreRole
     */
    public function setMembre(\AmbigussBundle\Entity\Membre $membre)
    {
        $this->membre = $membre;

        return $this;
    }

    /**
     * Get membre
     *
     * @return \AmbigussBundle\Entity\Membre
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * Set role
     *
     * @param \AmbigussBundle\Entity\Role $role
     *
     * @return MembreRole
     */
    public function setRole(\AmbigussBundle\Entity\Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AmbigussBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
