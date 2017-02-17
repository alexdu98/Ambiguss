<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\groupeRepository")
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
     * @ORM\Column(name="nom", type="string", length=16)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="id_groupe_parent", type="integer", nullable=true)
     */
    private $idGroupeParent;

    /**
     * @ORM\OneToMany(targetEntity="Membre", mappedBy="groupe")
     */
    private $membres;


    /**
     * @ORM\OneToMany(targetEntity="Droit_groupe", mappedBy="groupe")
     */
    private $droit_groupes;

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
     * @return groupe
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
     * Set idGroupeParent
     *
     * @param integer $idGroupeParent
     *
     * @return groupe
     */
    public function setIdGroupeParent($idGroupeParent)
    {
        $this->idGroupeParent = $idGroupeParent;

        return $this;
    }

    /**
     * Get idGroupeParent
     *
     * @return int
     */
    public function getIdGroupeParent()
    {
        return $this->idGroupeParent;
    }

    /**
     * @return mixed
     */
    public function getMembres()
    {
        return $this->membres;
    }

    /**
     * @param mixed $membres
     */
    public function setMembres($membres)
    {
        $this->membres = $membres;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->membres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add membre
     *
     * @param \AppBundle\Entity\Membre $membre
     *
     * @return Groupe
     */
    public function addMembre(\AppBundle\Entity\Membre $membre)
    {
        $this->membres[] = $membre;

        return $this;
    }

    /**
     * Remove membre
     *
     * @param \AppBundle\Entity\Membre $membre
     */
    public function removeMembre(\AppBundle\Entity\Membre $membre)
    {
        $this->membres->removeElement($membre);
    }

    /**
     * @return mixed
     */
    public function getDroitGroupes()
    {
        return $this->droit_groupes;
    }

    /**
     * @param mixed $droit_groupes
     */
    public function setDroitGroupes($droit_groupes)
    {
        $this->droit_groupes = $droit_groupes;
    }

    /**
     * Add droitGroupe
     *
     * @param \AppBundle\Entity\Droit_groupe $droitGroupe
     *
     * @return Groupe
     */
    public function addDroitGroupe(\AppBundle\Entity\Droit_groupe $droitGroupe)
    {
        $this->droit_groupes[] = $droitGroupe;

        return $this;
    }

    /**
     * Remove droitGroupe
     *
     * @param \AppBundle\Entity\Droit_groupe $droitGroupe
     */
    public function removeDroitGroupe(\AppBundle\Entity\Droit_groupe $droitGroupe)
    {
        $this->droit_groupes->removeElement($droitGroupe);
    }
}
