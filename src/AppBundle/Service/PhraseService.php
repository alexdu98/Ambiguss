<?php

namespace AppBundle\Service;

use AppBundle\Entity\Membre;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use Doctrine\ORM\EntityManagerInterface;
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

    public function new(Phrase $phrase, Membre $auteur, array $mapsRep, $isEdit = false)
    {
        $coutUnitaire = $this->container->getParameter('costCreatePhraseByMotAmbiguCredits');
        $gainCreation = $this->container->getParameter('gainCreatePhrasePoints');

        $phrase->setAuteur($auteur);
        $phrase->removeMotsAmbigusPhrase();

        $phrase->normalize();
        $res = $phrase->isValid();

        $succes = $res['succes'];
        $motsAmbigus = $res['motsAmbigus'] ?? array();

        if($succes && $auteur->getCredits() < $coutUnitaire * count($motsAmbigus)) {
            $res['succes'] = false;
            $res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($motsAmbigus) . " mots ambigus.";
        }

        if($succes) {
            $motAmbiguService = $this->container->get('AppBundle\Service\MotAmbiguService');
            $motAmbiguPhraseService = $this->container->get('AppBundle\Service\MotAmbiguPhraseService');

            $motAmbiguService->treatMotsAmbigus($phrase, $auteur, $motsAmbigus, $isEdit);

            // Mise à jour du nombre de crédits et de points de l'auteur
            $auteur->updateCredits(-$coutUnitaire * count($motsAmbigus));
            $auteur->updatePoints($gainCreation);

            $this->em->getConnection()->beginTransaction();

            $this->em->persist($phrase);
            $this->em->flush();

            // On enregistre dans l'historique de l'auteur
            $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
            $historiqueService->save($auteur, "Création de la phrase n°" . $phrase->getId() . " (+ " . $gainCreation . " points).");

            $motAmbiguPhraseService->treatMotsAmbigusPhrase($phrase, $auteur, $motsAmbigus, $mapsRep);

            $partie = new Partie();
            $partie->setJoueur($auteur);
            $partie->setPhrase($phrase);
            $partie->setJoue(true);
            $this->em->persist($partie);

            $this->em->flush();
            $this->em->getConnection()->commit();
        }

        return $res;
    }

    public function update(Phrase $phrase, Membre $modificateur, array $motsAmbigus, array $mapsRep)
    {
        $motAmbiguService = $this->container->get('AppBundle\Service\MotAmbiguService');
        $motAmbiguPhraseService = $this->container->get('AppBundle\Service\MotAmbiguPhraseService');
        $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

        $this->em->getConnection()->beginTransaction();

        // On enregistre dans l'historique du modificateur
        $historiqueService->save($modificateur, "Modification d'une phrase (n° " . $phrase->getId() . ").");
        // On enregistre dans l'historique de l'auteur
        $historiqueService->save($phrase->getAuteur(), "Modification d'une de vos phrase (n° " . $phrase->getId() . ").");

        $motAmbiguService->treatMotsAmbigus($phrase, $modificateur, $motsAmbigus, true);
        $newRep = $motAmbiguPhraseService->treatMotsAmbigusPhrase($phrase, $modificateur, $motsAmbigus, $mapsRep);

        $this->em->persist($phrase);
        $this->em->flush();
        $this->em->getConnection()->commit();

        $motAmbiguPhraseService->reorderMAP($phrase, $newRep);
    }

}
