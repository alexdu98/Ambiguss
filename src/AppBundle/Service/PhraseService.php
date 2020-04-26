<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\MotAmbiguPhrase;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use AppBundle\Event\AmbigussEvents;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PhraseService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function new(Phrase $phrase, Membre $auteur, array $mapRep)
    {
        $gainCreation = $this->container->getParameter('gainCreatePhrasePoints');

        $phrase->setAuteur($auteur);
        $phrase->removeMotsAmbigusPhrase();

        $phrase->normalizePreValidation();
        $res = $phrase->isValid();

        $motsAmbigus = $res['motsAmbigus'] ?? array();
        $pricePhrase = $this->getPrice(count($motsAmbigus));

        if($res['succes'] && !$this->isCreatable(count($motsAmbigus), $auteur->getCredits())) {
            $res['succes'] = false;
            $res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($motsAmbigus) . " mots ambigus.";
        }

        if($res['succes']) {

            $beforeReorder = $phrase->normalizePostValidation();

            try {
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

                $this->em->getConnection()->beginTransaction();

                $reps = $this->treat($phrase, $auteur, $beforeReorder, $mapRep, false);

                // Mise à jour du nombre de crédits et de points de l'auteur
                $auteur->updatePoints($gainCreation);
                $auteur->updateCredits(-$pricePhrase);

                $this->em->persist($phrase);
                $this->em->flush();

                // On enregistre dans l'historique de l'auteur
                $msg = "Création de la phrase n°" . $phrase->getId() . " (+" . $gainCreation . " points, -" . $pricePhrase . " crédits).";
                $historiqueService->save($auteur, $msg);

                $partie = new Partie();
                $partie->setJoueur($auteur);
                $partie->setPhrase($phrase);
                $partie->setJoue(true);
                $this->em->persist($partie);

                $this->em->flush();
                $this->em->getConnection()->commit();

                $ed = $this->container->get('event_dispatcher');

                $event = new GenericEvent(AmbigussEvents::POINTS_GAGNES, array(
                    'membre' => $auteur,
                ));
                $ed->dispatch(AmbigussEvents::POINTS_GAGNES, $event);

                /** @var MotAmbiguPhrase $map */
                foreach ($phrase->getMotsAmbigusPhrase() as $map) {
                    $map->addReponse($reps[$map->getOrdre()]);
                }
            }
            catch (UniqueConstraintViolationException $e) {
                $res['succes'] = false;
                $res['message'] = 'La phrase existe déjà';
            }
        }

        return $res;
    }

    public function update(Phrase $phrase, Membre $modificateur, Phrase $newPhrase, array $mapRep)
    {
        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
        $maps = $phrase->getMotsAmbigusPhrase();

        $phrase->setDateCreation(new \DateTime()); // Repousse le début de la jouabilité
        $phrase->setContenu($newPhrase->getContenu());
        $phrase->removeMotsAmbigusPhrase();

        $phrase->normalizePreValidation();
        $res = $phrase->isValid();

        $motsAmbigus = $res['motsAmbigus'] ?? array();
        $pricePhrase = $this->getPrice(count($motsAmbigus));

        if ($res['succes']) {
            $diffMA = count($motsAmbigus) - count($maps);
            $pricePhrase = $this->getPrice(count($maps)) - $pricePhrase;

            if (!$this->isCreatable($diffMA, $modificateur->getCredits())) {
                $res['succes'] = false;
                $res['message'] = "Vous n'avez pas assez de crédits pour ajouter " . $diffMA . " mots ambigus.";
            }
        }

        if($res['succes']) {
            $beforeReorder = $phrase->normalizePostValidation();

            try {
                $this->em->getConnection()->beginTransaction();

                // Supprime les anciens MAP
                foreach ($maps as $map) {
                    $this->em->remove($map);
                }

                $reps = $this->treat($phrase, $modificateur, $beforeReorder, $mapRep, true);

                // Mise à jour du nombre de crédits
                $phrase->getAuteur()->updateCredits($pricePhrase);

                // On enregistre dans l'historique de l'auteur
                $signe = $pricePhrase == 0 ? '-' : ($pricePhrase > 0 ? '+' : '');
                $msg = "Modification de la phrase n°" . $phrase->getId() . ' (' . $signe . $pricePhrase . " crédits).";
                $historiqueService->save($modificateur, $msg);

                $this->em->persist($phrase);
                $this->em->flush();
                $this->em->getConnection()->commit();

                /** @var MotAmbiguPhrase $map */
                foreach ($phrase->getMotsAmbigusPhrase() as $map) {
                    $map->addReponse($reps[$map->getOrdre()]);
                }
            }
            catch (UniqueConstraintViolationException $e) {
                $res['succes'] = false;
                $res['message'] = 'La phrase existe déjà';
            }
        }

        return $res;
    }

    public function updateModo(Phrase $phrase, Membre $modificateur, Phrase $formData, array $mapRep)
    {
        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

        $phrase->setContenu($formData->getContenu());
        $phrase->setSignale($formData->getSignale());
        $phrase->setVisible($formData->getVisible());
        $phrase->setDateModification(new \DateTime());
        $phrase->setModificateur($modificateur);

        $phrase->normalizePreValidation();
        $res = $phrase->isValid();

        $succes = $res['succes'];

        if($succes) {
            $beforeReorder = $phrase->normalizePostValidation();

            try {
                $this->em->getConnection()->beginTransaction();

                $reps = $this->treat($phrase, $modificateur, $beforeReorder, $mapRep, true);

                // On enregistre dans l'historique du modificateur
                $historiqueService->save($modificateur, "Modification d'une phrase (n° " . $phrase->getId() . ").", false, true);
                // On enregistre dans l'historique de l'auteur
                $historiqueService->save($phrase->getAuteur(), "Modification d'une de vos phrase (n° " . $phrase->getId() . ").");

                $this->em->persist($phrase);
                $this->em->flush();
                $this->em->getConnection()->commit();

                /** @var MotAmbiguPhrase $map */
                foreach ($phrase->getMotsAmbigusPhrase() as $map) {
                    $map->addReponse($reps[$map->getOrdre()]);
                }
            }
            catch (UniqueConstraintViolationException $e) {
                $res['succes'] = false;
                $res['message'] = 'La phrase existe déjà';
            }
        }

        return $res;
    }

    public function isCreatable($nbMA, $nbCredits)
    {
        return $nbCredits >= $this->getPrice($nbMA);
    }

    public function getPrice($nbMA)
    {
        return $nbMA * $this->container->getParameter('costCreatePhraseByMotAmbiguCredits');
    }

    public function treat(Phrase $phrase, Membre $auteur, $beforeReorder, $mapsRep, $isEdit)
    {
        $motAmbiguRepo = $this->em->getRepository('AppBundle:MotAmbigu');
        $repoGlose = $this->em->getRepository('AppBundle:Glose');

        // Si c'est une édition de phrase
        if($isEdit) {
            $mapsOri = array();
            // Duplication des MAP
            foreach($phrase->getMotsAmbigusPhrase() as $item) {
                $mapsOri[] = clone $item;
            }

            foreach($phrase->getMotsAmbigusPhrase() as $key => $map) {
                foreach($beforeReorder as $key2 => $motAmbigu) {
                    // Si le MAP est toujours présent, on passe au suivant
                    if($map->getOrdre() == $motAmbigu[1]) {
                        continue 2;
                    }
                }

                // Si l'ancien MAP n'a pas été trouvé, suppression
                $phrase->removeMotAmbiguPhrase($map);
                $this->em->remove($map);
            }
        }

        foreach($beforeReorder as $key => $ma)
        {
            $motAmbigu = new MotAmbigu();
            $motAmbigu->setValeur($ma[2]);
            $motAmbigu->setAuteur($auteur);

            $motAmbigu->normalize();

            $motAmbiguOBJ = $motAmbiguRepo->findOneBy(array('valeur' => $motAmbigu->getValeur()));

            if(!$motAmbiguOBJ){
                $this->em->persist($motAmbigu);
                $this->em->flush();

                $motAmbiguOBJ = $motAmbigu;
            }

            if($isEdit) {
                foreach($mapsOri as $key2 => $map) {
                    // Cas nouvel id exist dans ancienne phrase => MA update
                    if($map->getOrdre() == $ma[1])
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

        $reps = array();
        foreach($phrase->getMotsAmbigusPhrase() as $map) {

            // -1 car l'ordre commence à 1 et le reorder à 0
            $keyForMotsAmbigusPhrase = $beforeReorder[ $map->getOrdre() - 1 ][1];
            $idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
            if(empty($idGlose))
            {
                throw new \Exception("Tous les mots ambigus doivent avoir une glose");
            }
            $glose = $repoGlose->find($idGlose);

            $rep = new Reponse();
            $rep->setValeurGlose($glose->getValeur());
            $reps[$map->getOrdre()] = $rep;

            // S'il n'y a pas de réponse (nouveau MAP)
            if($map->getReponses()->count() == 0) {

                $rep->setContenuPhrase($phrase->getContenu());
                $rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
                $rep->setAuteur($auteur);
                $rep->setGlose($glose);
                $rep->setMotAmbiguPhrase($map);
                $rep->setPhrase($phrase);

                if(!$map->getMotAmbigu()->getGloses()->contains($glose))
                {
                    $map->getMotAmbigu()->addGlose($glose);
                }

                $this->em->persist($map);
                $this->em->persist($rep);
            }

            $map->getReponses()->clear();
        }

        return $reps;
    }

}
