<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phrase
 *
 * @ORM\Table(name="phrase", indexes={
 *     @ORM\Index(name="IDX_PHRASE_DATECREATION", columns={"date_creation"}),
 *     @ORM\Index(name="IDX_PHRASE_DATEMODIFICATION", columns={"date_modification"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PhraseRepository")
 */
class Phrase implements \JsonSerializable
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
     * @ORM\Column(name="contenu", type="string", length=1024)
     */
    private $contenu;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="contenu_pur", type="string", length=255, unique=true)
	 */
	private $contenuPur;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="gain_createur", type="integer")
	 */
	private $gainCreateur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    /**
     * @var bool
     *
     * @ORM\Column(name="signale", type="boolean")
     */
    private $signale;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre", inversedBy="phrases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

	/**
	 * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Membre")
	 */
	private $modificateur;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\MotAmbiguPhrase", mappedBy="phrase", cascade={"persist"})
	 */
	private $motsAmbigusPhrase;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\AimerPhrase", mappedBy="phrase", cascade={"persist"})
	 */
	private $likesPhrase;

	/**
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\Partie", mappedBy="phrase")
	 */
	private $parties;




    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->signale = 0;
        $this->visible = 1;
	    $this->gainCreateur = 0;
	    $this->motsAmbigusPhrase = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get dateCreation
     *
     * @return \DateTime
     */
	public function getDateCreation()
    {
	    return $this->dateCreation;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Phrase
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime
     */
	public function getDateModification()
    {
	    return $this->dateModification;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     *
     * @return Phrase
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

	/**
	 * Get gainCreateur
	 *
	 * @return int
	 */
	public function getGainCreateur()
	{
		return $this->gainCreateur;
	}

	/**
	 * Set gainCreateur
	 *
	 * @param integer $gainCreateur
	 *
	 * @return Phrase
	 */
	public function setGainCreateur($gainCreateur)
	{
		$this->gainCreateur = $gainCreateur;

		return $this;
	}

	/**
	 * Met à jour les gains
	 * @param $gainCreateur
	 *
	 * @return $this
	 */
    public function updateGainCreateur($gainCreateur){
    	$this->gainCreateur += $gainCreateur;
    	if($this->gainCreateur < 0)
		    $this->gainCreateur = 0;
    	return $this;
    }

    /**
     * Get signale
     *
     * @return bool
     */
	public function getSignale()
    {
	    return $this->signale;
    }

    /**
     * Set signale
     *
     * @param boolean $signale
     *
     * @return Phrase
     */
    public function setSignale($signale)
    {
        $this->signale = $signale;

        return $this;
    }

    /**
     * Get visible
     *
     * @return bool
     */
	public function getVisible()
    {
	    return $this->visible;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Phrase
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \UserBundle\Entity\Membre
     */
	public function getAuteur()
    {
	    return $this->auteur;
    }

    /**
     * Set auteur
     *
     * @param \UserBundle\Entity\Membre $auteur
     *
     * @return Phrase
     */
    public function setAuteur(\UserBundle\Entity\Membre $auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get modificateur
     *
     * @return \UserBundle\Entity\Membre
     */
	public function getModificateur()
    {
	    return $this->modificateur;
    }

    /**
     * Set modificateur
     *
     * @param \UserBundle\Entity\Membre $modificateur
     *
     * @return Phrase
     */
    public function setModificateur(\UserBundle\Entity\Membre $modificateur = null)
    {
        $this->modificateur = $modificateur;

        return $this;
    }

    /**
     * Add motAmbiguPhrase
     *
     * @param MotAmbiguPhrase $motAmbiguPhrase
     *
     * @return Phrase
     */
    public function addMotAmbiguPhrase(MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase[] = $motAmbiguPhrase;

        return $this;
    }

    /**
     * Remove motAmbiguPhrase
     *
     * @param MotAmbiguPhrase $motAmbiguPhrase
     */
    public function removeMotAmbiguPhrase(MotAmbiguPhrase $motAmbiguPhrase)
    {
        $this->motsAmbigusPhrase->removeElement($motAmbiguPhrase);
    }

	/**
	 * Remove motsAmbigusPhrase
	 */
	public function removeMotsAmbigusPhrase()
	{
		$this->motsAmbigusPhrase = new \Doctrine\Common\Collections\ArrayCollection();
	}

    /**
     * Get motsAmbigusPhrase
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotsAmbigusPhrase()
    {
        return $this->motsAmbigusPhrase;
    }

	public function getContenuAmb()
	{
		return preg_replace('#<amb id="([0-9]+)">(.*?)</amb>#', '<amb>$2</amb>', $this->getContenu());
	}

	/**
	 * Get contenu
	 *
	 * @return string
	 */
	public function getContenu()
	{
		return $this->contenu;
	}

	/**
	 * Set contenu
	 *
	 * @param string $contenu
	 *
	 * @return Phrase
	 */
	public function setContenu($contenu)
	{
		$this->contenu = $contenu;
		$this->setContenuPur($contenu);

		return $this;
	}

    /**
     * Add motsAmbigusPhrase
     *
     * @param MotAmbiguPhrase $motsAmbigusPhrase
     *
     * @return Phrase
     */
    public function addMotsAmbigusPhrase(MotAmbiguPhrase $motsAmbigusPhrase)
    {
        $this->motsAmbigusPhrase[] = $motsAmbigusPhrase;

        return $this;
    }

    /**
     * Add likesPhrase
     *
     * @param AimerPhrase $likesPhrase
     *
     * @return Phrase
     */
    public function addLikesPhrase(AimerPhrase $likesPhrase)
    {
        $this->likesPhrase[] = $likesPhrase;

        return $this;
    }

    /**
     * Remove likesPhrase
     *
     * @param AimerPhrase $likesPhrase
     */
    public function removeLikesPhrase(AimerPhrase $likesPhrase)
    {
        $this->likesPhrase->removeElement($likesPhrase);
    }

    /**
     * Get likesPhrase
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikesPhrase(){
	    return $this->likesPhrase;
    }

	/**
	 * Normalise la phrase
	 */
	public function normalize()
	{
		// Supprime les espaces multiples, option u car sinon les caractères utf8 partent en vrille
		$this->setContenu(preg_replace('#\s+#u', ' ', $this->getContenu()));

		// Met la première lettre en majuscule
		$this->setContenu(preg_replace_callback('#^(\<amb id\="[0-9]+"\>)?([a-z])(.*)#', function($matches)
		{
			return $matches[1] . strtoupper($matches[2]) . $matches[3];
		}, $this->getContenu()));

		// Ajoute le . final si non existant
		$last_letter = $this->getContenu()[ strlen($this->getContenu()) - 1 ];

		if($last_letter != '.' && $last_letter != '?' && $last_letter != '!')
		{
			$this->setContenu($this->getContenu() . '.');
		}
	}

	/**
	 * Check if phrase is valid
	 */
    public function isValid(){

    	// Pas d'autres balises html que <amb> et </amb>
    	if($this->getContenu() != strip_tags($this->getContenu(), '<amb>'))
		    return array('succes' => false, 'message' => 'Il ne faut que des balises <amb> et </amb>');

    	// Le même nombre de balise ouvrante et fermante
		$ambOuv = $ambFer = null;
	    $regexOuv = '#\<amb id\="([0-9]+)"\>#';
	    $regexFer = '#\</amb\>#';
	    preg_match_all($regexOuv, $this->getContenu(), $ambOuv, PREG_SET_ORDER);
	    preg_match_all($regexFer, $this->getContenu(), $ambFer, PREG_SET_ORDER);
	    if(count($ambOuv) != count($ambFer))
		    return array('succes' => false, 'message' => 'Il n\'y a pas le même nom de balise <amb> et </amb>');

	    // récupère les mots ambigus
	    $mots_ambigu = array();
	    $regex = '#\<amb id\="([0-9]+)"\>(.*?)\</amb\>#'; // Faux bug d'affichage PHPStorm, ne pas toucher
	    preg_match_all($regex, $this->getContenu(), $mots_ambigu, PREG_SET_ORDER);

	    // Au moins 1 mot ambigu
	    if(empty($mots_ambigu))
	    	return array('succes' => false, 'message' => 'Il faut au moins 1 mot ambigu');

	    // Pas plus de 10 mots ambigus
	    if(count($mots_ambigu) > 10)
		    return array(
			    'succes' => false,
			    'message' => 'Il ne faut pas dépasser 10 mots ambigus par phrase');

	    // Pas de balise imbriquée
	    foreach($mots_ambigu as $ma){
	    	if($ma[2] != strip_tags($ma[2]))
			    return array('succes' => false, 'message' => 'Il ne faut pas de balise imbriquée');
	    }

	    // Contenu pur ne dépassent pas 255 caractères
	    if(strlen($this->getContenuPur()) > 255)
	    {
		    return array(
			    'succes' => false,
			    'message' => 'La phrase est trop longue (255 caractères maximum hors balise <amb>)',
		    );
	    }

	    // Mot mal sélectionné
	    preg_match_all('#[a-zA-Z]\<amb|amb\>[a-zA-Z]#', $this->getContenu(), $arr, PREG_SET_ORDER);
	    if(!empty($arr))
	    {
		    return array(
			    'succes' => false,
			    'message' => 'Un mot était mal sélectionné (le caractère précédent une balise <amb> ou suivant une balise </amb> ne doit pas être une lettre).',
		    );
	    }

	    // Mot mal sélectionné
	    preg_match_all('#\<amb id\="[0-9]+"\> | \</amb\>#', $this->getContenu(), $arr, PREG_SET_ORDER);
	    if(!empty($arr))
	    {
		    return array(
			    'succes' => false,
			    'message' => 'Un mot était mal sélectionné (le caractère suivant une balise <amb> ou précédent une balise </amb> ne doit pas être un espace).',
		    );
	    }

	    // Pas de mot ambigu avec le même id
	    $temp = array();
	    foreach($mots_ambigu as $ma){
			$temp[$ma[1]] = null;
	    }
	    if(count($temp) !== count($mots_ambigu))
	    	return array('succes' => false, 'message' => 'Les mots ambigus doivent avoir des identifiants différents');

		// Réordonne les id
		foreach($mots_ambigu as $key => $ma){
			$regex = '#\<amb id\="' . $ma[1] . '"\>'. $ma[2] .'\</amb\>#';
			$newContenu = preg_replace($regex, '<amb id="' . ($key + 1) . '">' . $ma[2] . '</amb>', $this->getContenu());
			$this->setContenu($newContenu);
		}

	    return array('succes' => true, 'motsAmbigus' => $mots_ambigu);
    }

	/**
	 * Get contenu pur
	 *
	 * @return string
	 */
	public function getContenuPur()
	{
		return $this->contenuPur;
	}

	/**
	 * Set contenu pur
	 *
	 * @param string $contenu
	 *
	 * @return Phrase
	 */
	public function setContenuPur($contenu)
	{
		$this->contenuPur = strip_tags($contenu);

		return $this;
	}

	/**
	 * Add party
	 *
	 * @paramPartie $party
	 *
	 * @return Phrase
	 */
	public function addParty(Partie $party)
	{
		$this->parties[] = $party;

		return $this;
	}

	/**
	 * Remove party
	 *
	 * @param Partie $party
	 */
	public function removeParty(Partie $party)
	{
		$this->parties->removeElement($party);
	}

	/**
	 * Get parties
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getParties()
	{
		return $this->parties;
	}

	/**
	 * IMPLEMENTS JsonSerializable
	 */

	public function jsonSerialize()
	{
		$modificateur = !empty($this->modificateur) ? $this->modificateur->getPseudo() : '';
		$dateModification = !empty($this->dateModification) ? $this->dateModification->getTimestamp() : '';

		return array(
			$this->id,
			$this->getContenuHTML(),
			$this->auteur->getPseudo(),
			$this->dateCreation->getTimestamp(),
			$modificateur,
			$dateModification,
			$this->signale,
			$this->visible,
			$this->gainCreateur,
		);
	}

	/**
	 * AUTRES
	 */

	public function getContenuHTML()
	{
		return preg_replace('#<amb id="([0-9]+)">(.*?)</amb>#', '<b id="ma$1" class="ma color-red" title="Ce mot est ambigu (id : $1)">$2</b>',
			$this->getContenu());
	}

}
