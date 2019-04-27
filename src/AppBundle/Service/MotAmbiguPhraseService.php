<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MotAmbiguPhraseService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function treatMotsAmbigusPhrase(Phrase $phrase, Membre $auteur, array $motsAmbigus, array $mapsRep)
    {
        $repoGlose = $this->em->getRepository('AppBundle:Glose');

        $newRep = array();
        foreach($phrase->getMotsAmbigusPhrase() as $map) {

            // -1 car l'ordre commence à 1 et le reorder à 0
            $keyForMotsAmbigusPhrase = $motsAmbigus[ $map->getOrdre() - 1 ][1];
            $idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
            if(empty($idGlose))
            {
                throw new Exception("Tous les mots ambigus doivent avoir une glose");
            }
            $glose = $repoGlose->find($idGlose);

            $rep = new Reponse();
            $rep->setValeurGlose($glose->getValeur());
            $newRep[ $map->getOrdre() ] = $rep;

            // S'il n'y a pas de réponse (nouveau MA)
            if($map->getReponses()->count() == 0) {

                $rep->setContenuPhrase($phrase->getContenu());
                $rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
                $rep->setAuteur($auteur);
                $rep->setGlose($glose);
                $rep->setMotAmbiguPhrase($map);
                $rep->setPhrase($phrase);

                $map->addReponse($rep);

                if(!$map->getMotAmbigu()->getGloses()->contains($glose))
                {
                    $map->getMotAmbigu()->addGlose($glose);
                }

                $this->em->persist($map);
                $this->em->persist($rep);
            }
        }

        return $newRep;
    }

    public function removeMAP(Phrase $phrase, array $motsAmbigus)
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
            foreach($motsAmbigus as $key2 => $motAmbigu) {
                if($map->getOrdre() == $motAmbigu[1]) {
                    continue 2;
                }
            }

            // Cas ancien id not exist dans new phrase => MAP delete
            $phrase->removeMotAmbiguPhrase($map);
            $this->em->remove($map);
        }

        return $mapsOri;
    }

    public function reorderMAP(Phrase &$phrase, $newRep)
    {
        $maps = $phrase->getMotsAmbigusPhrase()->getValues();

        // Trie le tableau des motsAmbigusPhrase
        uasort($maps, function($a, $b)
        {
            return ($a->getOrdre() < $b->getOrdre()) ? -1 : 1;
        });

        // Supprime tous les MAP
        $phrase->removeMotsAmbigusPhrase();

        // Rajoute les MAP dans l'ordre
        foreach($maps as $key => $map)
        {
            $phrase->addMotAmbiguPhrase($maps[$key]);
        }

        foreach($phrase->getMotsAmbigusPhrase() as $key => $map)
        {
            $map->getReponses()->clear();
            $map->addReponse($newRep[$map->getOrdre()]);
        }
    }

}
