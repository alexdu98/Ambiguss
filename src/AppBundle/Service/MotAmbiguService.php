<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;

class MotAmbiguService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function findOrAdd($valeur, Membre $auteur)
    {
        $motAmbiguRepo = $this->em->getRepository('AppBundle:MotAmbigu');

        $motAmbigu = new MotAmbigu();
        $motAmbigu->setValeur($valeur);
        $motAmbigu->setAuteur($auteur);

        $motAmbigu->normalize();

        $tmp = $motAmbiguRepo->findOneBy(array('valeur' => $motAmbigu->getValeur()));
        
        if(!$tmp){
            $this->em->persist($motAmbigu);
            $this->em->flush();
            
            $tmp = $motAmbigu;
        }

        return $tmp;
    }

    public function treatForEditPhrase(Phrase $phrase, Membre $auteur, $motsAmbigus)
    {
        $mapsOri = array();
        foreach($phrase->getMotsAmbigusPhrase() as $item) {
            $mapsOri[] = clone $item;
        }

        /*
         * $motAmbigu[0] contient toute la balise <amb ... </amb>
         * $motAmbigu[1] contient l'id / l'ordre du mot ambigu
         * $motAmbigu[2] contient le mot ambigu
         */
        foreach($phrase->getMotsAmbigusPhrase() as $key => $map) {
            $find = false;
            foreach($motsAmbigus as $key2 => $motAmbigu) {
                if($map->getOrdre() == $motAmbigu[1]) {
                    $find = true;
                }
            }

            // Cas ancien id not exist dans new phrase => MAP delete
            if(!$find) {
                $this->em->remove($phrase->getMotsAmbigusPhrase()->get($key));
                $phrase->removeMotAmbiguPhrase($map);
            }
        }

        foreach($motsAmbigus as $key => $motAmbigu) {
            $motAmbiguOBJ = $this->findOrAdd($motAmbigu[2], $auteur);

            // Pour chaque ancien MAP
            foreach($mapsOri as $key2 => $map)
            {
                // Cas nouvel id exist dans ancienne phrase => MAP update
                if($map->getOrdre() == $motAmbigu[1])
                {
                    $phrase->getMotsAmbigusPhrase()->get($key2)->setMotAmbigu($motAmbiguOBJ);
                    $phrase->getMotsAmbigusPhrase()->get($key2)->setOrdre($key + 1);
                    continue 2;
                }
            }

            // Cas nouvel id not exist dans ancienne phrase => MAP add
            $map = new MotAmbiguPhrase();
            $map->setPhrase($phrase);
            $map->setOrdre($key + 1);
            $map->setMotAmbigu($motAmbigu);
            $phrase->addMotAmbiguPhrase($map);
        }
    }
}