<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Visite;
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
            // False si pas de visite dans la dernière période, la visite sinon
            $lastVisitPerdiod = $this->em->getRepository('AppBundle:Visite')->findLastVisitPeriod($this->timeBetweenTwoVisites);

            // S'il y avait déjà eu une visite, on récupère le nombre de secondes avant expiration
            if($lastVisitPerdiod){
                // La durée de vie du cookie est le reste de la durée de la période de la visite
                $time = $lastVisitPerdiod->getDateVisite()->getTimestamp() - (new \DateTime())->getTimeStamp();
                $time = $time + $this->timeBetweenTwoVisites;
            }
            // Sinon on enregistre la nouvelle visite
            else{
                $this->em->persist(new Visite());
                $this->em->flush();

                // La durée de vie du cookie est la durée d'une période
                $time = $this->timeBetweenTwoVisites;
            }

            // Enregistrement du cookie
            setcookie('visite', 'visited', time() + $time);
        }
	}

}
