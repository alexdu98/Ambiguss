<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * historique
 *
 * @ORM\Table(name="historique")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\historiqueRepository")
 */
class Historique
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
     * @ORM\Column(name="valeur", type="string", length=256)
     */
    private $valeur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_action", type="date")
     */
    private $dateAction;

    /**
     * @var int
     *
     * @ORM\Column(name="id_membre", type="integer", nullable=true)
     */
    private $idMembre;

    /**
    * @ORM\ManyToOne(targetEntity="Membre", inversedBy="historiques")
    * @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
     */
    private $membre;



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
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return historique
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime
     */
    public function getDateAction()
    {
        return $this->dateAction;

    }

    /**
     * @return mixed
     */
    public function getMembre()
    {
        return $this->membre;
    }

    /**
     * @param mixed $membre
     */
    public function setMembre($membre)
    {
        $this->membre = $membre;
    }

    /**
     * @return mixed
     */
    public function getIdMembre()
    {
        return $this->idMembre;
    }

    /**
     * @param mixed $idMembre
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;
    }

    

    /**
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Historique
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }
}
