<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoidsReponse
 *
 * @ORM\Table(name="poids_reponse")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PoidsReponseRepository")
 */
class PoidsReponse
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
     * @var int
     *
     * @ORM\Column(name="poids_reponse", type="smallint", unique=true)
     */
    private $poidsReponse;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="label", type="string", length=32, unique=true)
	 */
    private $label;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="ordre", type="smallint", unique=true)
	 */
	private $ordre;


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
     * Set poidsReponse
     *
     * @param integer $poidsReponse
     *
     * @return PoidsReponse
     */
    public function setPoidsReponse($poidsReponse)
    {
        $this->poidsReponse = $poidsReponse;

        return $this;
    }

    /**
     * Get poidsReponse
     *
     * @return int
     */
    public function getPoidsReponse()
    {
        return $this->poidsReponse;
    }

	/**
	 * Set label
	 *
	 * @param string $label
	 *
	 * @return PoidsReponse
	 */
	public function setLabel($label)
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * Get label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Set ordre
	 *
	 * @param integer $ordre
	 *
	 * @return PoidsReponse
	 */
	public function setOrdre($ordre)
	{
		$this->ordre = $ordre;

		return $this;
	}

	/**
	 * Get ordre
	 *
	 * @return int
	 */
	public function getOrdre()
	{
		return $this->ordre;
	}
}

