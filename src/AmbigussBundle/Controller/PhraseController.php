<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\AimerPhrase;
use AmbigussBundle\Entity\MotAmbigu;
use AmbigussBundle\Entity\MotAmbiguPhrase;
use AmbigussBundle\Entity\Partie;
use AmbigussBundle\Entity\Reponse;
use AmbigussBundle\Form\GloseAddType;
use AmbigussBundle\Form\PhraseAddType;
use AmbigussBundle\Form\PhraseEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Historique;

class PhraseController extends Controller
{

	public function addAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$phrase = new \AmbigussBundle\Entity\Phrase();
			$form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);

			$form->handleRequest($request);

			$newPhrase = null;
			$edit = false;
			if($form->isSubmitted() && $form->isValid())
			{
				$data = $form->getData();

				$edit = empty($_POST['phrase_id']) ? false : true;

				$phrase->setAuteur($this->getUser());
				$phrase->removeMotsAmbigusPhrase();

				// Normalise la phrase
				$phrase->normalize();

				// Vérifie que la phrase soit bien formée
				$res = $phrase->isValid();
				$succes = $res['succes'];

				$phrD = null;
				if(!$edit && $succes)
				{
					if($this->getUser()->getCredits() < $this->getParameter('costCreatePhraseByMotAmbiguCredits') * count($res['motsAmbigus']))
					{
						$succes = false;
						$res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($res['motsAmbigus']) . " mots ambigus.";
					}
				}
				else
				{
					if($edit)
					{
						$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
						$phrD = $repoP->find($_POST['phrase_id']);

						$dateMax = $phrD->getDateCreation()->getTimestamp() + $this->getParameter('dureeAvantJouabiliteSecondes');
						$dateActu = new \DateTime();
						$dateActu = $dateActu->getTimestamp();

						// Si jamais il modifie le champ à la main il peut changé n'importe quelle phrase
						if($phrD->getAuteur() != $this->getUser() || !$this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
						{
							$succes = false;
							$res['message'] = "Vous ne pouvez pas modifier cette phrase car vous n'êtes pas son auteur.";
						}

						if($dateActu > $dateMax && $phrD->getParties()->count() > 1)
						{
							$succes = false;
							$res['message'] = "Les " . $this->getParameter('dureeAvantJouabiliteSecondes') . " secondes sont passées et un joueur a joué votre phrase, vous ne pouvez donc plus la modifier. Signalez-là, un modérateur s'occupera de votre demande.";
						}
					}
				}

				if($succes === true)
				{

					$mots_ambigu = $res['motsAmbigus'];

					/*
					 * $mots_ambigu[0] contient un array du premier match
					 * $mots_ambigu[1] contient un array du deuxieme match...
					 *
					 * $mots_ambigu[][0] contient toute la balise <amb ... </amb>
					 * $mots_ambigu[][1] contient l'id / l'ordre du mot ambigu
					 * $mots_ambigu[][2] contient le mot ambigu
					 */
					$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
					foreach($mots_ambigu as $key => $mot_ambigu)
					{
						// Soit on le trouve dans la BD soit on l'ajoute
						$mot_ambigu_OBJ = new MotAmbigu();
						$mot_ambigu_OBJ->setValeur($mot_ambigu[2]);
						$mot_ambigu_OBJ->setAuteur($this->getUser());
						// Normalise le mot ambigu
						$mot_ambigu_OBJ->normalize();
						$mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu_OBJ);

						$map = new MotAmbiguPhrase();
						$map->setOrdre($key + 1);
						$map->setPhrase($phrase);
						$map->setMotAmbigu($mot_ambigu_OBJ);

						$phrase->addMotAmbiguPhrase($map);
					}

					if(!$edit)
					{
						$phrase->getAuteur()->updateCredits(-$this->getParameter('costCreatePhraseByMotAmbiguCredits') * count($mots_ambigu));
						$phrase->getAuteur()->updatePoints($this->getParameter('gainCreatePhrasePoints'));
					}

					$em = $this->getDoctrine()->getManager();
					$em->getConnection()->beginTransaction();
					try
					{

						$em->persist($phrase);
						$em->flush();

						// On enregistre dans l'historique du joueur
						$histJoueur = new Historique();
						if(!$edit)
						{
							$histJoueur->setValeur("Création de la phrase n°" . $phrase->getId() . ".");
						}
						else
						{
							$histJoueur->setValeur("Modification d'une phrase (n° " . $_POST['phrase_id'] . " => n°" . $phrase->getId() . ").");
						}
						$histJoueur->setMembre($this->getUser());
						$em->persist($histJoueur);

						$repository1 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
						$repository2 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
						$repository3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');

						$mapsRep = $request->request->get('phrase_add')['motsAmbigusPhrase'];

						foreach($phrase->getMotsAmbigusPhrase() as $map)
						{
							$rep = new Reponse();
							$rep->setContenuPhrase($phrase->getContenu());
							$rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
							// -1 car l'ordre commence à 1 et le reorder à 0
							$keyForMotsAmbigusPhrase = $mots_ambigu[ $map->getOrdre() - 1 ][1];
							$idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
							if(empty($idGlose))
							{
								throw new \Exception("Tous les mots ambigus doivent avoir une glose");
							}
							$glose = $repository3->find($idGlose);
							$rep->setValeurGlose($glose->getValeur());
							$rep->setAuteur($this->getUser());
							$poidsReponse = $mapsRep[ $keyForMotsAmbigusPhrase ]['poidsReponse'];
							$rep->setPoidsReponse($repository1->find($poidsReponse));
							$rep->setNiveau($repository2->findOneByTitre('Facile'));
							$rep->setGlose($glose);
							$rep->setMotAmbiguPhrase($map);

							$map->addReponse($rep);

							if(!$map->getMotAmbigu()->getGloses()->contains($glose))
							{
								$map->getMotAmbigu()->addGlose($glose);
							}

							$em->persist($map);
							$em->persist($rep);
						}

						$partie = new Partie();
						$partie->setJoueur($this->getUser());
						$partie->setPhrase($phrase);
						$partie->setJoue(true);
						$em->persist($partie);

						$newPhrase = $phrase;

						if($edit)
						{
							$em->remove($phrD);
						}

						$em->flush();
						$em->getConnection()->commit();

						// Réinitialise le formulaire
						$phrase = new \AmbigussBundle\Entity\Phrase();
						$form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);
					}
					catch(\Exception $e)
					{
						$em->getConnection()->rollBack();
						$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $e->getMessage());
					}
				}
				else
				{
					$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $res['message']);
				}
			}

			$glose = new \AmbigussBundle\Entity\Glose();
			$addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
				'action' => $this->generateUrl('ambiguss_glose_add'),
			));

			if($edit)
			{
				return $this->render('AmbigussBundle:Phrase:edit.html.twig', array(
					'form' => $form->createView(),
					'newPhrase' => $newPhrase,
					// en cas d'edit ne doit pas etre false
					'phrase' => $newPhrase,
					'addGloseForm' => $addGloseForm->createView(),
				));
			}

			return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
				'form' => $form->createView(),
				'newPhrase' => $newPhrase,
				// en cas d'edit ne doit pas etre false
				'phrase' => false,
				'addGloseForm' => $addGloseForm->createView(),
			));
		}
		else
		{

			$this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");

			return $this->redirectToRoute('user_connexion');
		}
	}

	public
	function likeAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
	{

		$rep = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:AimerPhrase');
		$aimerPhrase = $rep->findOneBy(array(
			'phrase' => $phrase,
			'membre' => $this->getUser(),
		));

		$em = $this->getDoctrine()->getManager();

		$action = null;
		if(!$aimerPhrase)
		{
			$aimerPhrase = new AimerPhrase();
			$aimerPhrase->setPhrase($phrase)->setMembre($this->getUser());
			// ajoute X points au créateur
			$aimerPhrase->getPhrase()->getAuteur()->updatePoints($this->getParameter('gainPerLikePhrasePoints'));
			$action = 'like';

			// On enregistre dans l'historique du joueur
			$histJoueur = new Historique();
			$histJoueur->setValeur("Aime la phrase n°" . $phrase->getId() . ".");
			$histJoueur->setMembre($this->getUser());

			// On enregistre dans l'historique du createur de la phrase
			$histAuteur = new Historique();
			$histAuteur->setValeur("Un joueur a aimé votre phrase n°" . $phrase->getId() . " (+" . $this->getParameter('gainPerLikePhrasePoints') .
			                       " points).");
			$histAuteur->setMembre($phrase->getAuteur());

			$em->persist($histJoueur);
			$em->persist($histAuteur);
		}
		else
		{
			if($aimerPhrase->getActive() === false)
			{
				$aimerPhrase->setActive(true);
				$action = 'relike';
			}
			else
			{
				$aimerPhrase->setActive(false);
				$action = 'unlike';
			}
		}

		$em->persist($aimerPhrase);
		$em->flush();

		return $this->json(array(
			'status' => 'succes',
			'action' => $action,
		));
	}

	public
	function SignalAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
	{
		if($phrase->getSignale() == 0)
		{
			$phrase->setSignale(1);
			$action = 'signal';
		}
		else
		{
			$phrase->setSignale(0);
			$action = 'cancel';
		}
		$em = $this->getDoctrine()->getManager();
		$em->persist($phrase);
		$em->flush();

		return $this->json(array(
			'status' => 'succes',
			'action' => $action,
		));

	}

	public function moderationAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
				$phrases = $repo->getSignale(array(
					'signale' => true,
				));

				return $this->render('AmbigussBundle:Phrase:getAll.html.twig', array(
					'phrases' => $phrases,
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function keepAction(\AmbigussBundle\Entity\Phrase $phrase)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				try
				{
					$phrase->setSignale(0);
					$em = $this->getDoctrine()->getEntityManager();
					$em->persist($phrase);
					$em->flush();

					return $this->json(array(
						'succes' => true,
					));
				}
				catch(\Exception $e)
				{
					return $this->json(array(
						'succes' => false,
						'message' => $e,
					));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function deleteAction(\AmbigussBundle\Entity\Phrase $phrase)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				try
				{
					$phrase->setVisible(0);
					$em = $this->getDoctrine()->getEntityManager();
					$em->persist($phrase);
					$em->flush();

					return $this->json(array(
						'succes' => true,
					));
				}
				catch(\Exception $e)
				{
					return $this->json(array(
						'succes' => false,
						'message' => $e,
					));
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function editAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
	{
		$dateMax = $phrase->getDateCreation()->getTimestamp() + $this->getParameter('dureeAvantJouabiliteSecondes');
		$dateActu = new \DateTime();
		$dateActu = $dateActu->getTimestamp();
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR') || ($phrase->getAuteur() == $this->getUser() && $dateMax < $dateActu))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				if($dateActu < $dateMax)
				{
					$phr = new \AmbigussBundle\Entity\Phrase();
					$form = $this->get('form.factory')->create(PhraseAddType::class, $phr, array('action' => $this->generateUrl('ambiguss_phrase_add')));

					$form->handleRequest($request);
					$newPhrase = null;

					$glose = new \AmbigussBundle\Entity\Glose();
					$addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
						'action' => $this->generateUrl('ambiguss_glose_add'),
					));

					return $this->render('AmbigussBundle:Phrase:edit.html.twig', array(
						'form' => $form->createView(),
						'newPhrase' => $newPhrase,
						'phrase' => $phrase,
						'addGloseForm' => $addGloseForm->createView(),
					));
				}
				else if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
				{
					$phr = new \AmbigussBundle\Entity\Phrase();
					$form = $this->get('form.factory')->create(PhraseEditType::class, $phr);

					$form->handleRequest($request);

					$phraseOri = clone $phrase;
					$newPhrase = null;

					if($form->isSubmitted() && $form->isValid())
					{
						$data = $form->getData();

						$phrase->setDateModification(new \DateTime());
						$phrase->setModificateur($this->getUser());
						$phrase->setSignale($data->getSignale());

						// Normalise la phrase
						$data->normalize();

						// Vérifie que la phrase soit bien formée
						$res = $data->isValid();
						$succes = $res['succes'];

						$phrase->setContenu($data->getContenu());

						if($succes === true)
						{
							/*
							 * $mots_ambigu[0] contient un array du premier match
							 * $mots_ambigu[1] contient un array du deuxieme match...
							 *
							 * $mots_ambigu[][0] contient toute la balise <amb ... </amb>
							 * $mots_ambigu[][1] contient l'id / l'ordre du mot ambigu
							 * $mots_ambigu[][2] contient le mot ambigu
							 */
							$mots_ambigu = $res['motsAmbigus'];

							$em = $this->getDoctrine()->getManager();

							$mapsOri = array();
							foreach($phrase->getMotsAmbigusPhrase() as $item)
							{
								$mapsOri[] = clone $item;
							}

							foreach($phrase->getMotsAmbigusPhrase() as $key => $map)
							{
								$find = false;
								foreach($mots_ambigu as $key2 => $mot_ambigu)
								{
									if($map->getOrdre() == $mot_ambigu[1])
									{
										$find = true;
									}
								}

								// Cas ancien id not exist dans new phrase => MA delete
								if(!$find)
								{
									$em->remove($phrase->getMotsAmbigusPhrase()->get($key));
									$phrase->removeMotAmbiguPhrase($map);
								}
							}

							$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
							foreach($mots_ambigu as $key => $mot_ambigu)
							{
								// Soit on le trouve dans la BD soit on l'ajoute
								$mot_ambigu_OBJ = new MotAmbigu();
								$mot_ambigu_OBJ->setValeur($mot_ambigu[2]);
								$mot_ambigu_OBJ->setAuteur($this->getUser());
								// Normalise le mot ambigu
								$mot_ambigu_OBJ->normalize();
								$mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu_OBJ);

								// Pour chaque ancien MA
								foreach($mapsOri as $key2 => $map)
								{
									// Cas nouvel id exist dans ancienne phrase => MA update
									if($map->getOrdre() == $mot_ambigu[1])
									{
										$phrase->getMotsAmbigusPhrase()->get($key2)->setMotAmbigu($mot_ambigu_OBJ);
										$phrase->getMotsAmbigusPhrase()->get($key2)->setOrdre($key + 1);
										continue 2;
									}
								}

								// Cas nouvel id not exist dans ancienne phrase => MA add
								$map = new MotAmbiguPhrase();
								$map->setPhrase($phrase);
								$map->setOrdre($key + 1);
								$map->setMotAmbigu($mot_ambigu_OBJ);
								$phrase->addMotAmbiguPhrase($map);
								$maps[] = $map;
							}

							$em->getConnection()->beginTransaction();
							try
							{

								$em->persist($phrase);
								$em->flush();

								// On enregistre dans l'historique du modificateur
								$histModificateur = new Historique();
								$histModificateur->setValeur("Modification d'une phrase (n° " . $phrase->getId() . ").");
								$histModificateur->setMembre($this->getUser());
								$em->persist($histModificateur);

								// On enregistre dans l'historique de l'auteur
								$histAuteur = new Historique();
								$histAuteur->setValeur("Modification d'une de vos phrase (n° " . $phrase->getId() . ").");
								$histAuteur->setMembre($phrase->getAuteur());
								$em->persist($histAuteur);

								$repository1 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
								$repository2 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
								$repository3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');

								$mapsRep = $request->request->get('phrase_edit')['motsAmbigusPhrase'];

								foreach($phrase->getMotsAmbigusPhrase() as $map)
								{
									// Si il n'y a pas de réponse (nouveau MA)
									if($map->getReponses()->count() == 0)
									{
										$rep = new Reponse();
										$rep->setContenuPhrase($phrase->getContenu());
										$rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
										// -1 car l'ordre commence à 1 et le reorder à 0
										$keyForMotsAmbigusPhrase = $mots_ambigu[ $map->getOrdre() - 1 ][1];
										$idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
										if(empty($idGlose))
										{
											throw new \Exception("Tous les mots ambigus doivent avoir une glose");
										}
										$glose = $repository3->find($idGlose);
										$rep->setValeurGlose($glose->getValeur());
										$rep->setAuteur($this->getUser());
										$poidsReponse = $mapsRep[ $keyForMotsAmbigusPhrase ]['poidsReponse'];
										$rep->setPoidsReponse($repository1->find($poidsReponse));
										$rep->setNiveau($repository2->findOneByTitre('Facile'));
										$rep->setGlose($glose);
										$rep->setMotAmbiguPhrase($map);

										$map->addReponse($rep);

										if(!$map->getMotAmbigu()->getGloses()->contains($glose))
										{
											$map->getMotAmbigu()->addGlose($glose);
										}

										$em->persist($map);
										$em->persist($rep);
									}
								}

								$em->flush();
								$em->getConnection()->commit();

								// Ordonne les MAP
								$maps = $phrase->getMotsAmbigusPhrase()->getValues();

								// Trie le tableau des motsAmbigusPhrase dans l'ordre croissant de l'ordre d'apparition
								uasort($maps, function($a, $b)
								{
									return ($a->getOrdre() < $b->getOrdre()) ? -1 : 1;
								});

								$phrase->removeMotsAmbigusPhrase();

								foreach($maps as $map)
								{
									$phrase->addMotAmbiguPhrase($map);
								}

								$newPhrase = $phrase;

								// Réinitialise le formulaire
								$phrase = new \AmbigussBundle\Entity\Phrase();
								$form = $this->get('form.factory')->create(PhraseEditType::class, $phrase);
							}
							catch(\Exception $e)
							{
								$em->getConnection()->rollBack();
								$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $e->getMessage());
							}
						}
						else
						{
							$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $res['message']);
						}
					}

					$repoJ = $this->getDoctrine()->getManager()->getRepository('JudgmentBundle:Jugement');
					$repoTO = $this->getDoctrine()->getManager()->getRepository('JudgmentBundle:TypeObjet');

					$typeObj = $repoTO->findOneBy(array('typeObjet' => 'Phrase'));
					$jugements = $repoJ->findBy(array(
						'typeObjet' => $typeObj,
						'verdict' => null,
						'idObjet' => $phrase->getId(),
					));

					$glose = new \AmbigussBundle\Entity\Glose();
					$addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
						'action' => $this->generateUrl('ambiguss_glose_add'),
					));

					return $this->render('AmbigussBundle:Phrase:editModerateur.html.twig', array(
						'form' => $form->createView(),
						'phrase' => $phrase,
						'phraseOri' => $phraseOri,
						'newPhrase' => $newPhrase,
						'jugements' => $jugements,
						'addGloseForm' => $addGloseForm->createView(),
					));
				}
				else
				{
					throw $this->createAccessDeniedException();
				}
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}
}
