<?php

namespace AppBundle\Listeners;

use AppBundle\Services\Visite;

class VisiteListener
{

	private $visite;

	public function __construct(Visite $visite)
	{
		$this->visite = $visite;
	}

	public function process(){
		$this->visite->checkAndAdd();
	}

}