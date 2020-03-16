<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MembreBadge
 *
 * @ORM\Table(
 *     name="membre_badge",
 *     indexes={
 *         @ORM\Index(name="ix_mbrebadg_mbreid", columns={"membre_id"}),
 *         @ORM\Index(name="ix_mbrebadg_badgid", columns={"badge_id"}),
 *         @ORM\Index(name="ix_mbrebadg_dtobt", columns={"date_obtention"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_mbrebadg_mbreidbadgid", columns={"membre_id", "badge_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MembreBadgeRepository")
 */
class MembreBadge
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_obtention", type="datetime")
     */
    private $dateObtention;

    /**
     * @ORM\ManyToOne(targetEntity="Membre", inversedBy="badges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $membre;

    /**
     * @ORM\ManyToOne(targetEntity="Badge", inversedBy="membres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $badge;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateObtention()
    {
        return $this->dateObtention;
    }

    /**
     * @param \DateTime $dateObtention
     */
    public function setDateObtention($dateObtention)
    {
        $this->dateObtention = $dateObtention;

        return $this;
    }

    /**
     * @return Membre
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

        return $this;
    }

    /**
     * @return Badge
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param mixed $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->membre;
    }

}
