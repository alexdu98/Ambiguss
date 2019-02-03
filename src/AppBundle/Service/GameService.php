<?php

namespace AppBundle\Service;

use AppBundle\Entity\Game;
use AppBundle\Entity\MotAmbiguPhrase;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getGame(?Phrase $phrase)
    {
        $phraseRepo = $this->em->getRepository('AppBundle:Phrase');
        $partieRepo = $this->em->getRepository('AppBundle:Partie');

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $alreadyPlayed = false;

        if($phrase) {
            if($phrase->isJouable($this->container->getParameter('dureeAvantJouabiliteSecondes')) ) {
                $alreadyPlayed = $partieRepo->findOneBy(array(
                    'joueur' => $user,
                    'phrase' => $phrase,
                    'joue' => true,
                ));
            }
            else {
                $phrase = null;
            }
        }

        if(!$phrase) {
            if($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
            {
                $phrases = $phraseRepo->findIdPhrasesNotPlayedByMembre($user, $this->container->getParameter('dureeAvantJouabiliteSecondes'));
            }

            // Si toutes les phrases ont été joués ou que le joueur n'est pas connecté
            if(empty($phrases))
            {
                $alreadyPlayed = true;
                $phrases = $phraseRepo->findRandom($this->container->getParameter('dureeAvantJouabiliteSecondes'));
            }

            // Prend une phrase au hasard dans la liste des phrases jouables
            $phrase_id = $phrases[ array_rand($phrases) ]['id'];
            $phrase = $phraseRepo->find($phrase_id);
        }

        $game = new Game();
        $game->phrase = $phrase;
        $game->alreadyPlayed = $alreadyPlayed;
        /** @var MotAmbiguPhrase $map */
        foreach ($phrase->getMotsAmbigusPhrase() as $map) {
            $reponse = new Reponse();
            $reponse->setMotAmbiguPhrase($map);
            $reponse->setPhrase($phrase);
            $reponse->setContenuPhrase($phrase->getContenu());
            $reponse->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
            $game->reponses->add($reponse);
        }

        return $game;
    }

    public function isValid($data)
    {
        foreach ($data->reponses as $key => $rep) {
            if (!$rep->getGlose()) {
                return false;
            }
        }

        return true;
    }

    public function calculateResult($reponse)
    {
        $reponseRepo = $this->em->getRepository('AppBundle:Reponse');

        // Calcul le nombre de votes pour chaque glose du mot ambigu
        $gloses = array();
        $nbTotalVotes = 0;
        foreach ($reponse->getMotAmbiguPhrase()->getMotAmbigu()->getGloses() as $g) {
            $compteur = $reponseRepo->findByIdPMAetGloses($reponse->getMotAmbiguPhrase(), $g);
            $isSelected = $g->getValeur() == $reponse->getValeurGlose() ? true : false;
            $gloseRes = array(
                'nbVotes' => $compteur['nbVotes'],
                'isSelected' => $isSelected,
            );
            $gloses[$g->getValeur()] = $gloseRes;
            $nbTotalVotes += $gloses[$g->getValeur()]['nbVotes'];
        }

        // Trie le tableau des gloses dans l'ordre décroissant du nombre de réponses
        uasort($gloses, function ($a, $b) {
            if ($a['nbVotes'] == $b['nbVotes']) {
                return 0;
            }

            return ($a['nbVotes'] > $b['nbVotes']) ? -1 : 1;
        });

        // Tableau contenant pour chaque mot ambigu, le nombre de vote de chacune des gloses associées
        $resMAP = array(
            'valeurMA' => $reponse->getValeurMotAmbigu(),
            'gloses' => $gloses,
        );

        return array(
            'resMAP' => $resMAP,
            'nbPoints' => ($nbTotalVotes > 0) ? (($gloses[$reponse->getValeurGlose()]['nbVotes'] / $nbTotalVotes) * 100) : 0
        );
    }

}
