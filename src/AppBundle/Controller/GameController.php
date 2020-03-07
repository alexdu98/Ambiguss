<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Form\Game\GameType;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Entity\Signalement;
use AppBundle\Form\Signalement\SignalementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GameController extends Controller
{

    public function playAction(Request $request, Phrase $phrase)
    {
        $em = $this->getDoctrine()->getManager();
        $gameService = $this->get('AppBundle\Service\GameService');

        // Récupération de la partie jouée
        $game = $gameService->getGame($phrase);
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $gameService->isValid($form->getData())) {
            $data = $form->getData();

            // Traitement de chaque réponse
            $stats = array();
            $nbPoints = 0;
            foreach ($data->reponses as $rep) {
                $rep->setValeurGlose($rep->getGlose()->getValeur());

                // Si l'utilisateur est connecté on enregisgtre la réponse
                if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $rep->setAuteur($this->getUser());
                    $em->persist($rep);
                }

                // Récupère les votes et les points gagnés pour chaque MAP
                $result = $gameService->calculateResult($rep);
                $stats[$rep->getMotAmbiguPhrase()->getOrdre()] = $result['resMAP'];
                $nbPoints += $result['nbPoints'];
            }

            $alreadyPlayed = false;
            $nextMembresClassements = null;
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $partieRepo = $em->getRepository('AppBundle:Partie');
                $partie = $partieRepo->findOneBy(array(
                    'phrase' => $phrase,
                    'joueur' => $this->getUser()
                ));

                // Si le joueur n'avait pas déjà joué la phrase
                if (empty($partie) || $partie->getJoue() == false) {
                    // On lui ajoute les points et crédits
                    $gainJoueur = ceil($nbPoints);
                    $this->getUser()->updatePoints($gainJoueur);
                    $this->getUser()->updateCredits($gainJoueur);

                    // On ajoute les points et crédits au créateur de la phrase
                    $gainCreateur = ceil(($gainJoueur * $this->getParameter('gainPercentByGame')) / 100);
                    $phrase->getAuteur()->updatePoints($gainCreateur);
                    $phrase->getAuteur()->updateCredits($gainCreateur);

                    // On enregistre la partie
                    if (empty($partie)) {
                        $partie = new Partie();
                        $partie->setPhrase($phrase);
                        $partie->setJoueur($this->getUser());
                    }
                    $partie->setJoue(true);
                    $partie->setGainJoueur($gainJoueur);

                    // On donne des points au créateur de la phrase
                    $phrase->updateGainCreateur($gainCreateur);

                    $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

                    // On enregistre dans l'historique du joueur
                    $historiqueService->save($this->getUser(), "Vous avez joué la phrase n°" . $phrase->getId() . " (+" . $gainJoueur . " crédits/points).");

                    // On enregistre dans l'historique du createur de la phrase
                    $historiqueService->save($phrase->getAuteur(), "Un joueur a joué votre phrase n°" . $phrase->getId() . " (+" . $gainCreateur . " crédits/points).");

                    $em->persist($partie);
                    $em->persist($this->getUser());
                    $em->persist($phrase->getAuteur());

                    $em->flush();
                }
                else {
                    $alreadyPlayed = true;
                }

                $nextMembresClassements = $em->getRepository('AppBundle:Membre')->getNextMembresClassements($this->getUser());
            }

            // Formulaire de création de signalement
            $addSignalementForm = $this->createForm(SignalementAddType::class, new Signalement(), array(
                'action' => $this->generateUrl('api_signalement_new'),
            ));

            return $this->render('AppBundle:Game:after_play.html.twig', array(
                'phrase' => $phrase,
                'stats' => $stats,
                'alreadyPlayed' => $alreadyPlayed,
                'nbPoints' => ceil($nbPoints),
                'nextMembresClassements' => $nextMembresClassements,
                'addSignalementForm' => $addSignalementForm->createView(),
            ));
        }

        $this->get('session')->getFlashBag()->add('erreur', "Formulaire invalide");
        return $this->redirectToRoute('game_show');
    }

    public function showAction(Phrase $phrase = null)
    {
        $em = $this->getDoctrine()->getManager();
        $gameService = $this->get('AppBundle\Service\GameService');

        // Récupération de la phrase à jouer et création du formulaire de jeu
        $game = $gameService->getGame($phrase);
        $form = $this->createForm(GameType::class, $game, array(
            'action' => $this->generateUrl('game_play', array('id' => $game->phrase->getId()))
        ));

        // Si le joueur est connecté, tente de récupérer le jaime
        $liked = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $jaimeRepo = $em->getRepository('AppBundle:JAime');
            $liked = $jaimeRepo->findOneBy(array(
                'membre' => $this->getUser(),
                'phrase' => $phrase,
                'active' => true
            ));
        }

        // Formulaire de création de glose
        $addGloseForm = $this->createForm(GloseAddType::class, new Glose(), array(
            'action' => $this->generateUrl('api_glose_new'),
        ));

        // Formulaire de création de signalement
        $addSignalementForm = $this->createForm(SignalementAddType::class, new Signalement(), array(
            'action' => $this->generateUrl('api_signalement_new'),
        ));

        return $this->render('AppBundle:Game:play.html.twig', array(
            'form' => $form->createView(),
            'phrase' => $game->phrase,
            'alreadyPlayed' => $game->alreadyPlayed,
            'liked' => $liked,
            'addGloseForm' => $addGloseForm->createView(),
            'addSignalementForm' => $addSignalementForm->createView(),
        ));
    }

}
