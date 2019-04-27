<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\MotAmbiguPhrase;

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

    public function treatMotsAmbigus(Phrase $phrase, Membre $auteur, array $motsAmbigus, $isEdit = false)
    {
        if($isEdit) {
            $motAmbiguPhraseService = $this->container->get('AppBundle\Service\MotAmbiguPhraseService');
            $mapsOri = $motAmbiguPhraseService->removeMAP($phrase, $motsAmbigus);
        }
        /*
         * $motsAmbigus[0] contient un array du premier match
         * $motsAmbigus[1] contient un array du deuxieme match...
         *
         * $motsAmbigus[][0] contient toute la balise <amb ... </amb>
         * $motsAmbigus[][1] contient l'id / l'ordre du mot ambigu
         * $motsAmbigus[][2] contient le mot ambigu
         */
        foreach($motsAmbigus as $key => $motAmbigu)
        {
            $motAmbiguService = $this->container->get('AppBundle\Service\MotAmbiguService');

            $motAmbiguOBJ = $motAmbiguService->findOrAdd($motAmbigu[2], $auteur);

            if($isEdit) {
                foreach($mapsOri as $key2 => $map) {
                    // Cas nouvel id exist dans ancienne phrase => MA update
                    if($map->getOrdre() == $motAmbigu[1])
                    {
                        $phrase->getMotsAmbigusPhrase()->get($key2)->setMotAmbigu($motAmbiguOBJ);
                        $phrase->getMotsAmbigusPhrase()->get($key2)->setOrdre($key + 1);
                        continue 2;
                    }
                }
            }

            $map = new MotAmbiguPhrase();
            $map->setOrdre($key + 1);
            $map->setPhrase($phrase);
            $map->setMotAmbigu($motAmbiguOBJ);

            $phrase->addMotAmbiguPhrase($map);
        }
    }
}
