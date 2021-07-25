<?php

namespace App\Controller;

use App\Entity\Glose;
use App\Entity\Partie;
use App\Entity\Phrase;
use App\Event\GameEvents;
use App\Form\Game\GameType;
use App\Form\Glose\GloseAddType;
use App\Entity\Signalement;
use App\Form\Signalement\SignalementAddType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class GameController extends AbstractController
{

    public function play(Request $request, Phrase $phrase, GameService $gameService)
    {
        $em = $this->getDoctrine()->getManager();

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
                $partieRepo = $em->getRepository('App:Partie');
                $partie = $partieRepo->findOneBy(array(
                    'phrase' => $phrase,
                    'joueur' => $this->getUser()
                ));

                // Si le joueur n'avait pas déjà joué la phrase
                if (empty($partie) || $partie->getJoue() == false) {
                    $ed = $this->get('event_dispatcher');

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

                    $historiqueService = $this->container->get('App\Service\HistoriqueService');

                    // On enregistre dans l'historique du joueur
                    $historiqueService->save($this->getUser(), "Vous avez joué la phrase n°" . $phrase->getId() . " (+" . $gainJoueur . " crédits/points).");

                    // On enregistre dans l'historique du createur de la phrase
                    $historiqueService->save($phrase->getAuteur(), "Un joueur a joué votre phrase n°" . $phrase->getId() . " (+" . $gainCreateur . " crédits/points).");

                    $em->persist($partie);
                    $em->persist($this->getUser());
                    $em->persist($phrase->getAuteur());

                    $em->flush();

                    $event = new GenericEvent(GameEvents::GAME_PLAYED, array(
                        'membre' => $this->getUser(),
                        'phrase' => $phrase
                    ));
                    $ed->dispatch(GameEvents::GAME_PLAYED, $event);

                    $event = new GenericEvent(GameEvents::POINTS_GAGNES, array(
                        'membre' => $phrase->getAuteur(),
                    ));
                    $ed->dispatch(GameEvents::POINTS_GAGNES, $event);

                    $event = new GenericEvent(GameEvents::POINTS_GAGNES, array(
                        'membre' => $this->getUser(),
                    ));
                    $ed->dispatch(GameEvents::POINTS_GAGNES, $event);
                }
                else {
                    $alreadyPlayed = true;
                }

                $nextMembresClassements = $em->getRepository('App:Membre')->getNextMembresClassements($this->getUser());
            }

            // Formulaire de création de signalement
            $addSignalementForm = $this->createForm(SignalementAddType::class, new Signalement(), array(
                'action' => $this->generateUrl('api_signalement_new'),
            ));

            return $this->render('App:Game:after_play.html.twig', array(
                'phrase' => $phrase,
                'stats' => $stats,
                'alreadyPlayed' => $alreadyPlayed,
                'nbPoints' => ceil($nbPoints),
                'nextMembresClassements' => $nextMembresClassements,
                'addSignalementForm' => $addSignalementForm->createView(),
            ));
        }

        $this->get('session')->getFlashBag()->add('danger', "Formulaire invalide");
        return $this->redirectToRoute('game_show');
    }

    public function show(Phrase $phrase = null, GameService $gameService)
    {
        $em = $this->getDoctrine()->getManager();

        // Récupération de la phrase à jouer et création du formulaire de jeu
        $game = $gameService->getGame($phrase);
        $form = $this->createForm(GameType::class, $game, array(
            'action' => $this->generateUrl('game_play', array('id' => $game->phrase->getId()))
        ));

        // Si le joueur est connecté, tente de récupérer le jaime
        $liked = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $jaimeRepo = $em->getRepository('App:JAime');
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

        return $this->render('Game/play.html.twig', array(
            'form' => $form->createView(),
            'phrase' => $game->phrase,
            'alreadyPlayed' => $game->alreadyPlayed,
            'liked' => $liked,
            'addGloseForm' => $addGloseForm->createView(),
            'addSignalementForm' => $addSignalementForm->createView(),
        ));
    }

}
