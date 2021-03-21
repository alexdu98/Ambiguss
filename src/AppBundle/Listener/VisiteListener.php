<?php

namespace AppBundle\Listener;

use AppBundle\Entity\Visite;
use AppBundle\Util\Bitwise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Cookie;

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

    /**
     * Vérifie si le visiteur à déjà été comptabilisé et le fait si ce n'est pas le cas
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function process(GetResponseEvent $event)
    {
        // Si il n'y a pas de cookie de visite
        if (!$event->getRequest()->cookies->has('visite')) {
            $visite = new Visite();
            $visite->setIp($event->getRequest()->server->get('REMOTE_ADDR'));
            $visite->setUserAgent($event->getRequest()->server->get('HTTP_USER_AGENT'));

            // False si pas de visite dans la dernière période, la visite sinon
            $lastVisitPerdiod = $this->em->getRepository('AppBundle:Visite')->findLastVisitPeriod($visite, $this->timeBetweenTwoVisites);

            // S'il y avait déjà eu une visite, on récupère le nombre de secondes avant expiration
            if ($lastVisitPerdiod) {
                // La durée de vie du cookie est le reste de la durée de la période de la visite
                $time = $lastVisitPerdiod->getDateVisite()->getTimestamp() - (new \DateTime())->getTimeStamp();
                $time = $time + $this->timeBetweenTwoVisites;
            } // Sinon on enregistre la nouvelle visite
            else {
                $this->em->persist($visite);
                $this->em->flush();

                // La durée de vie du cookie est la durée d'une période
                $time = $this->timeBetweenTwoVisites;
            }

            $cookieInfo = $event->getRequest()->cookies->has('cookieInfo');
            // Si les cookies Ambiguss sont acceptés
            if ($cookieInfo && Bitwise::isSet('COOKIE_INFO', $cookieInfo, 'ambiguss')) {
                // Enregistrement du cookie de visite
                setcookie('visite', 'true', array(
                    'expires' => time() + $time,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ));
            }
        }
    }

}
