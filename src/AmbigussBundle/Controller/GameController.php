<?php
/**
 * Created by PhpStorm.
 * User: MELY
 * Date: 3/13/2017
 * Time: 2:32 PM
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\Game;
use AmbigussBundle\Entity\Partie;
use AmbigussBundle\Form\GameType;
use AmbigussBundle\Form\GloseAddType;
use JudgmentBundle\Entity\Jugement;
use JudgmentBundle\Form\JugementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Historique;

class GameController extends Controller
{

	public function mainAction(Request $request, \AmbigussBundle\Entity\Phrase $id = null)
	{
		$game = new Game();
		$form = $this->get('form.factory')->create(GameType::class, $game);

		$phraseOBJ = null;
		$motsAmbigus = null;
		$phraseHTMLEscape = null;
		$allPhrasesPlayed = null;
		$signal = null;
		$isAuteur = null;

		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');
			$repository2 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
			$repository3 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');

			$em = $this->getDoctrine()->getManager();
			$valid = true;
			foreach($data->reponses as $key => $rep)
			{
				if(!$rep->getGlose())
				{
					$valid = false;
					break;
				}
				$rep->setMotAmbiguPhrase($repository->find($request->request->get('ambigussbundle_game')
				                                           ['reponses'][ $key ]['idMotAmbiguPhrase']));
				$rep->setContenuPhrase($rep->getMotAmbiguPhrase()->getPhrase()->getContenu());
				$rep->setValeurMotAmbigu($rep->getMotAmbiguPhrase()->getMotAmbigu()->getValeur());
				$rep->setValeurGlose($rep->getGlose()->getValeur());
				if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
				{
					$rep->setAuteur($this->getUser());
				}
				$rep->setPoidsReponse($repository2->findOneBy(array('poidsReponse' => 1)));
				$rep->setNiveau($repository3->findOneBy(array('titre' => 'Facile')));

				if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
				{
					$em->persist($rep);
				}
			}

			// Si tous les mots ambigus ont une glose associée
			if($valid)
			{
				$hash = array();
				$map = array();
				$nb_points = 0;
				$repo4 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
				foreach($data->reponses as $rep)
				{
					$map[] = $rep->getMotAmbiguPhrase()->getId();
					$gloses = array();
					$total = 0;
					foreach($rep->getMotAmbiguPhrase()->getMotAmbigu()->getGloses() as $g)
					{
						$compteur = $repo4->findByIdPMAetGloses($rep->getMotAmbiguPhrase(), $g->getId());
						$isSelected = $g->getValeur() == $rep->getValeurGlose() ? true : false;
						$ar2 = array(
							'nbVotes' => $compteur['nbVotes'],
							'isSelected' => $isSelected,
						);
						$gloses[ $g->getValeur() ] = $ar2;
						$total = $total + $gloses[ $g->getValeur() ]['nbVotes'];
					}
					// Trie le tableau des gloses dans l'ordre décroissant du nombre de réponses
					uasort($gloses, function($a, $b)
					{
						if($a['nbVotes'] == $b['nbVotes'])
						{
							return 0;
						}

						return ($a['nbVotes'] > $b['nbVotes']) ? -1 : 1;
					});
					$resMA = array(
						'valeurMA' => $rep->getValeurMotAmbigu(),
						'gloses' => $gloses,
					);
					if($total > 0)
					{
						$nb_points = $nb_points + (($gloses[ $rep->getValeurGlose() ]['nbVotes'] / $total) * 100);
					}
					$hash[ $rep->getMotAmbiguPhrase()->getOrdre() ] = $resMA;
				}

				$alreadyPlayed = false;
				if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
				{
					$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Partie');
					$partie = $repoP->findOneBy(array(
						'phrase' => $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase(),
						'joueur' => $this->getUser(),
					));

					// Si le joueur n'avait pas déjà joué la phrase
					if(empty($partie) || ($partie->getJoue() == 0 && $partie->getPhrase()->getAuteur() != $this->getUser()))
					{
						// On lui ajoute les points et crédits au joueur
						$gainJoueur = ceil($nb_points);
						$this->getUser()->updatePoints($gainJoueur);
						$this->getUser()->updateCredits($gainJoueur);

						// On vérifie le niveau du joueur
						$niveauSuivant = $this->getUser()->getNiveau()->getNiveauParent();
						if($niveauSuivant != null && $this->getUser()->getPointsClassement() >= $niveauSuivant->getPointsClassementMin())
						{
							$this->getUser()->setNiveau($this->getUser()->getNiveau()->getNiveauParent());
						}

						// On ajoute les points et crédits au createur de la phrase
						$gainCreateur = ceil(($nb_points * $this->getParameter('gainPercentByGame')) / 100);
						$auteur = $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getAuteur();
						$auteur->updatePoints($gainCreateur);
						$auteur->updateCredits($gainCreateur);

						// On vérifie le niveau du createur
						$niveauSuivant = $auteur->getNiveau()->getNiveauParent();
						if($niveauSuivant != null && $auteur->getPointsClassement() >= $niveauSuivant->getPointsClassementMin())
						{
							$auteur->setNiveau($auteur->getNiveau()->getNiveauParent());
						}

						// On enregistre la partie
						if(empty($partie))
						{
							$partie = new Partie();
							$partie->setPhrase($data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase());
							$partie->setJoueur($this->getUser());
						}

						$partie->setJoue(true);
						$partie->setGainJoueur(ceil($nb_points));
						$partie->getPhrase()->setGainCreateur($gainCreateur);

						// On enregistre dans l'historique du joueur
						$histJoueur = new Historique();
						$histJoueur->setValeur("Vous avez joué la phrase n°" . $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getId()
						                       . " (+" . ceil($nb_points) . " crédits/points).");
						$histJoueur->setMembre($this->getUser());

						// On enregistre dans l'historique du createur de la phrase
						$histAuteur = new Historique();
						$histAuteur->setValeur("Un joueur a joué votre phrase n°" . $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getId() . " (+" . $gainCreateur . " crédits).");
						$histAuteur->setMembre($auteur);

						$em->persist($partie);
						$em->persist($histJoueur);
						$em->persist($histAuteur);
						$em->persist($this->getUser());
						$em->persist($auteur);

						try
						{
							$em->flush();
						}
						catch(\Exception $e)
						{
							$this->get('session')->getFlashBag()->add('erreur', "Erreur insertion");
						}
					}
					elseif($partie->getPhrase()->getAuteur() == $this->getUser()){
						$isAuteur = true;
					}
					else
					{
						$alreadyPlayed = true;
					}
				}

				$this->get('session')->getFlashBag()->add('phrase_id', $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getId());
				$this->get('session')->getFlashBag()->add('phrase', $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getContenuHTML());
				$this->get('session')->getFlashBag()->add('auteur', $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getAuteur());
				$this->get('session')->getFlashBag()->add('stats', $hash);
				$this->get('session')->getFlashBag()->add('alreadyPlayed', $alreadyPlayed);
				$this->get('session')->getFlashBag()->add('isAuteur', $isAuteur);
				$this->get('session')->getFlashBag()->add('nb_points', ceil($nb_points));

				return $this->redirectToRoute('ambiguss_game_result');
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "Tous les mots ambigus doivent avoir une glose");
			}
		}
		else
		{
			$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
			$repmap = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

			$phrases = null;
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
			{
				$phrases = $repository->findIdPhrasesNotPlayedByMembre($this->getUser(), $this->getParameter('dureeAvantJouabiliteSecondes'));
			}
			else
			{
				$date = new \DateTime();
				// Date d'il y a 3 jours
				$date = $date->getTimestamp() - (3600 * 24 * 3);
				$phrases = $repmap->findIdPhrasesNotPlayedByIpSince($_SERVER['REMOTE_ADDR'], $date, 100, $this->getParameter('dureeAvantJouabiliteSecondes'));
			}

			// Si toutes les phrases ont été joués
			$allPhrasesPlayed = false;
			if(empty($phrases))
			{
				$allPhrasesPlayed = true;
				$phrases = $repmap->findAllIdPhrases($this->getParameter('dureeAvantJouabiliteSecondes'));
			}

			// Rend une clé au hasard
			if($id == null)
			{
				$phrase_id = $phrases[ array_rand($phrases) ]['id'];
				$phraseOBJ = $repository->find($phrase_id);
			}
			else
			{
				$phrase_id = $id;

				$phraseOBJ = $repository->find($phrase_id);

				$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Partie');
				if($repoP->findOneBy(array(
					'joueur' => $this->getUser(),
					'phrase' => $phraseOBJ,
					'joue' => true,
				))
				)
				{
					$allPhrasesPlayed = true;
				}
			}

			// recup champ signal
			$signal = $phraseOBJ->getSignale();

			$phraseHTMLEscape = preg_replace('#"#', '\"', $phraseOBJ->getContenuHTML());

			$motsAmbigus = array();
			foreach($phraseOBJ->getMotsAmbigusPhrase() as $key => $map)
			{
				$motsAmbigus[] = array(
					$map->getMotAmbigu()->getValeur(),
					$map->getId(),
					$map->getOrdre(),
				);
			}
		}

		// récupère le like d'un membre
		$liked = null;
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$rep = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:AimerPhrase');
			$liked = $rep->findOneBy(array(
				'membre' => $this->getUser(),
				'phrase' => $phraseOBJ,
			));
			if($liked)
			{
				$liked = $liked->getActive();
			}
		}

		$glose = new \AmbigussBundle\Entity\Glose();
		$addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
			'action' => $this->generateUrl('ambiguss_glose_add'),
		));

		// jugement (cas signalement)
		$jug = new Jugement();
		$addJugementForm = $this->get('form.factory')->create(JugementAddType::class, $jug, array(
			'action' => $this->generateUrl('jugement_add'),
		));

		// Ordonne les mots ambigus sur leur ordre
		uasort($motsAmbigus, function($a, $b)
		{
			return ($a[2] < $b[2]) ? -1 : 1;
		});
		$motsAmbigus = array_values($motsAmbigus);

		return $this->render('AmbigussBundle:Game:play.html.twig', array(
			'form' => $form->createView(),
			'phrase_id' => $phraseOBJ->getId(),
			'motsAmbigus' => json_encode($motsAmbigus),
			'phraseHTMLEscape' => $phraseHTMLEscape,
			'phrasePur' => preg_replace('#"#', '\"', $phraseOBJ->getContenuPur()),
			'liked' => $liked,
			'alreadyPlayed' => $allPhrasesPlayed,
			'signal' => $signal,
			'auteur' => $phraseOBJ->getAuteur(),
			'addGloseForm' => $addGloseForm->createView(),
			'addJugementForm' => $addJugementForm->createView(),
		));
	}

	public function resultatAction(Request $request)
	{
		$phrase_id = $this->get('session')->getFlashBag()->get('phrase_id');
		$phrase = $this->get('session')->getFlashBag()->get('phrase');
		$auteur = $this->get('session')->getFlashBag()->get('auteur');
		$isAuteur = $this->get('session')->getFlashBag()->get('isAuteur');
		$stats = $this->get('session')->getFlashBag()->get('stats');
		$alreadyPlayed = $this->get('session')->getFlashBag()->get('alreadyPlayed');
		$nb_points = $this->get('session')->getFlashBag()->get('nb_points');

		if(!empty($phrase_id) && !empty($phrase) && !empty($auteur) && !empty($isAuteur) && !empty($stats) && !empty($alreadyPlayed) && !empty($nb_points))
		{
			// jugement (cas signalement)
			$jug = new Jugement();
			$addJugementForm = $this->get('form.factory')->create(JugementAddType::class, $jug, array(
				'action' => $this->generateUrl('jugement_add'),
			));

			return $this->render('AmbigussBundle:Game:after_play.html.twig', array(
				'phrase' => $phrase[0],
				'auteur' => $auteur[0],
				'isAuteur' => $isAuteur[0],
				'stats' => $stats[0],
				'alreadyPlayed' => $alreadyPlayed[0],
				'nb_point' => $nb_points[0],
				'phraseHTMLEscape' => preg_replace('#"#', '\"', $phrase[0]),
				'addJugementForm' => $addJugementForm->createView(),
				'phrase_id' => $phrase_id[0],
			));
		}
		throw $this->createNotFoundException();
	}
}