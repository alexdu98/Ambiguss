<?php

namespace AppBundle\Listeners;

use AppBundle\Service\Visite;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class VisiteListener implements EventSubscriberInterface
{

	private $visite;

	public function __construct(Visite $visite)
	{
		$this->visite = $visite;
	}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
    }

	public function process(){
		$this->visite->checkAndAdd();
	}

}