<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * historique
 *
 * @ORM\Table(name="historique")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\historiqueRepository")
 */
class historique
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
     * @ORM\Column(name="label_historique", type="string", length=256)
     */
    private $labelHistorique;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_action", type="date")
     */
    private $dateAction;


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
     * Set labelHistorique
     *
     * @param string $labelHistorique
     *
     * @return historique
     */
    public function setLabelHistorique($labelHistorique)
    {
        $this->labelHistorique = $labelHistorique;

        return $this;
    }

    /**
     * Get labelHistorique
     *
     * @return string
     */
    public function getLabelHistorique()
    {
        return $this->labelHistorique;
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
}

