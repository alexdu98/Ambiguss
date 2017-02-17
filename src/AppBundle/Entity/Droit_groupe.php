<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droit_groupe
 *
 * @ORM\Table(name="droit_groupe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Droit_groupeRepository")
 */
class Droit_groupe
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
     * @ORM\Column(name="type", type="string", length=8)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="cible", type="string", length=32)
     */
    private $cible;

    /**
     * @var bool
     *
     * @ORM\Column(name="autoriser", type="boolean")
     */
    private $autoriser;

    /**
     * @var int
     *
     * @ORM\Column(name="id_groupe", type="integer", nullable=true)
     */
    private $idGroupe;

    /**
     * @ORM\ManyToOne(targetEntity="Groupe", inversedBy="droit_groupes")
     * @ORM\JoinColumn(name="id_groupe", referencedColumnName="id")
     */
    private $groupe;


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
     * Set type
     *
     * @param string $type
     *
     * @return Droit_groupe
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set cible
     *
     * @param string $cible
     *
     * @return Droit_groupe
     */
    public function setCible($cible)
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * Get cible
     *
     * @return string
     */
    public function getCible()
    {
        return $this->cible;
    }

    /**
     * Set autoriser
     *
     * @param boolean $autoriser
     *
     * @return Droit_groupe
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
     * Set idGroupe
     *
     * @param integer $idGroupe
     *
     * @return Droit_groupe
     */
    public function setIdGroupe($idGroupe)
    {
        $this->idGroupe = $idGroupe;

        return $this;
    }

    /**
     * Get idGroupe
     *
     * @return int
     */
    public function getIdGroupe()
    {
        return $this->idGroupe;
    }

    /**
     * @return mixed
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * @param mixed $groupe
     */
    public function setGroupe($groupe)
    {
        $this->groupe = $groupe;
    }
    
    
}
