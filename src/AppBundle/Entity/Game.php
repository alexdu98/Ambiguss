<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Game{

	public $reponses = array();

	public function __construct(){
		$this->reponses = new ArrayCollection();
	}

}