<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\MotAmbiguPhrase;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhraseService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function new(Phrase $phrase, Membre $auteur, array $mapRep, $isEdit = false)
    {
        $coutUnitaire = $this->container->getParameter('costCreatePhraseByMotAmbiguCredits');
        $gainCreation = $this->container->getParameter('gainCreatePhrasePoints');

        $phrase->setAuteur($auteur);
        $phrase->removeMotsAmbigusPhrase();

        $phrase->normalizePreValidation();
        $res = $phrase->isValid();
        $beforeReorder = $phrase->normalizePostValidation();

        $succes = $res['succes'];
        $motsAmbigus = $res['motsAmbigus'] ?? array();

        if($succes && $auteur->getCredits() < $coutUnitaire * count($motsAmbigus)) {
            $res['succes'] = false;
            $res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($motsAmbigus) . " mots ambigus.";
        }

        if($succes) {
            // Mise à jour du nombre de crédits et de points de l'auteur
            $auteur->updateCredits(-$coutUnitaire * count($motsAmbigus));
            $auteur->updatePoints($gainCreation);

            try {
                $this->em->getConnection()->beginTransaction();

                $reps = $this->treat($phrase, $auteur, $beforeReorder, $mapRep, false);

                $this->em->persist($phrase);
                $this->em->flush();

                // On enregistre dans l'historique de l'auteur
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
                $historiqueService->save($auteur, "Création de la phrase n°" . $phrase->getId() . " (+ " . $gainCreation . " points).");

                $partie = new Partie();
                $partie->setJoueur($auteur);
                $partie->setPhrase($phrase);
                $partie->setJoue(true);
                $this->em->persist($partie);

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

    public function update(Phrase $phrase, Membre $modificateur, Phrase $formData, array $mapRep)
    {
        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

        $phrase->setContenu($formData->getContenu());
        $phrase->setSignale($formData->getSignale());
        $phrase->setVisible($formData->getVisible());
        $phrase->setDateModification(new \DateTime());
        $phrase->setModificateur($modificateur);

        $phrase->normalizePreValidation();
        $res = $phrase->isValid();
        $beforeReorder = $phrase->normalizePostValidation();

        $succes = $res['succes'];

        if($succes) {
            try {
                $this->em->getConnection()->beginTransaction();

                $reps = $this->treat($phrase, $modificateur, $beforeReorder, $mapRep, true);

                // On enregistre dans l'historique du modificateur
                $historiqueService->save($modificateur, "Modification d'une phrase (n° " . $phrase->getId() . ").");
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
