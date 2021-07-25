<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Game{

    public $phrase;
	public $reponses;
	public $alreadyPlayed;

	public function __construct(){
		$this->reponses = new ArrayCollection();
	}

}
