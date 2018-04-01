<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique
 *
 * @ORM\Table(name="historique", indexes={
 *     @ORM\Index(name="IDX_HISTORIQUE_VALEUR", columns={"valeur"}),
 *     @ORM\Index(name="IDX_HISTORIQUE_DATEACTION", columns={"date_action"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoriqueRepository")
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
     * @ORM\Column(name="date_action", type="datetime")
     */
    private $dateAction;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="historiques")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $membre;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateAction = new \DateTime();
    }

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
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return Historique
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get membre
     *
     * @return Membre
     */
	public function getMembre()
    {
	    return $this->membre;
    }

    /**
     * Set membre
     *
     * @param Membre $membre
     *
     * @return Historique
     */
    public function setMembre(Membre $membre)
    {
        $this->membre = $membre;

        return $this;
    }

    public function __toString()
    {
        return $this->valeur;
    }


}
