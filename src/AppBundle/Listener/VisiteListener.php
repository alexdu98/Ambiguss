<?php

namespace AppBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class VisiteListener implements EventSubscriberInterface
{

    private $em;
    private $timeBetweenTwoVisites;

	public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
	{
	    $this->em = $entityManager;
        $this->timeBetweenTwoVisites = $container->getParameter('timeBetweenTwoVisitesSecondes');
	}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
    }

	public function process(){
	    // Si il n'y a pas de cookie de visite
        if (empty($_COOKIE['visite']))
        {
            // La durée de vie du cookie est celle par défaut
            $time = $this->timeBetweenTwoVisites;
            // Si il y avait déjà eu une visite, on récupère le nombre de secondes avant expiration
            if(($next = $this->em->getRepository('AppBundle:Visite')->checkVisite($time)) !== true){
                $time = $next;
            }
            setcookie('visite', 'visited', time() + $time);
        }
	}

}