<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\GroupeRepository")
 */
class Groupe
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
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe")
     */
    private $groupeParent;


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
     * @return Groupe
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

    /**
     * Set groupeParent
     *
     * @param \UserBundle\Entity\Groupe $groupeParent
     *
     * @return Groupe
     */
    public function setGroupeParent(\UserBundle\Entity\Groupe $groupeParent = null)
    {
        $this->groupeParent = $groupeParent;

        return $this;
    }

    /**
     * Get groupeParent
     *
     * @return \UserBundle\Entity\Groupe
     */
    public function getGroupeParent()
    {
        return $this->groupeParent;
    }

    /**     
     * Set roles        
     *      
     * @param array $roles      
     *      
     * @return Groupe       
     */     
    public function setRoles($roles)        
    {       
        $this->roles = $roles;      
        
        return $this;       
    }       
        
    /**     
     * Get roles        
     *      
     * @return array        
     */     
    public function getRoles()      
    {       
        return $this->roles;        
    }
}
