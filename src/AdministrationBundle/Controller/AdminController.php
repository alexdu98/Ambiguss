<?php

namespace AdministrationBundle\Controller;

use AdministrationBundle\Form\SearchGloseType;
use AdministrationBundle\Form\SearchMembreType;
use AdministrationBundle\Form\SearchPhraseType;
use AmbigussBundle\Form\GloseEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Historique;
use UserBundle\Form\MembreType;

class AdminController extends Controller
{

	public function mainAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				return $this->render('AdministrationBundle:Administration:accueil.html.twig', array());
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function statistiquesAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$stat = array();

				$repoV = $this->getDoctrine()->getManager()->getRepository('AppBundle:Visite');
				$stat['visites'] = $repoV->getStat();

				$repoM = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
				$stat['membres'] = $repoM->getStat();

				$repoPh = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
				$stat['phrases'] = $repoPh->getStat();

				$repoMA = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
				$stat['motsAmbigus'] = $repoMA->getStat();

				$repoG = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
				$stat['gloses'] = $repoG->getStat();

				$repoPa = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Partie');
				$stat['parties'] = $repoPa->getStat();

				$repoR = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
				$stat['reponses'] = $repoR->getStat();

				$repoJ = $this->getDoctrine()->getManager()->getRepository('JudgmentBundle:Jugement');
				$stat['jugements'] = $repoJ->getStat();

				return $this->render('AdministrationBundle:Administration:statistiques.html.twig', array(
					'stat' => $stat,
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function membresAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$form = $this->get('form.factory')->create(SearchMembreType::class, null, array(
					'action' => $this->generateUrl('administration_membre_edit', array('id' => null)),
				));

				return $this->render('AdministrationBundle:Administration:membres.html.twig', array(
					'form' => $form->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function editMembreAction(Request $request, \UserBundle\Entity\Membre $user)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$oldUser = clone($user);

				$form = $this->get('form.factory')->create(MembreType::class, $user);
				$form->handleRequest($request);

				if($form->isSubmitted() && $form->isValid())
				{
					if(!empty($user->getMdp()))
					{
						// Hash le Mdp
						$encoder = $this->get('security.password_encoder');
						$hash = $encoder->encodePassword($user, $user->getMdp());

						$user->setMdp($hash);
					}
					else
					{
						$user->setMdp($oldUser->getMdp());
					}

					// On enregistre dans l'historique du joueur
					$histJoueur = new Historique();
					$histJoueur->setValeur("Profil modifié par un administrateur");
					$histJoueur->setMembre($user);

					// On enregistre dans l'historique de l'admin
					$histAdmin = new Historique();
					$histAdmin->setValeur("Profil " . $user->getId() . " modifié");
					$histAdmin->setMembre($this->getUser());

					$em = $this->getDoctrine()->getManager();
					$em->persist($histJoueur);
					$em->persist($histAdmin);

					try
					{
						$em->flush();
						$this->get('session')->getFlashBag()->add('succes', 'Membre mis à jour avec succès');
					}
					catch(\Exception $e)
					{
						$this->get('session')->getFlashBag()->add('erreur', 'Erreur');
					}
				}

				return $this->render('AdministrationBundle:Administration:membre_edit.html.twig', array(
					'form' => $form->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function phrasesAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$form = $this->get('form.factory')->create(SearchPhraseType::class);

				$form->handleRequest($request);

				if($form->isSubmitted() && $form->isValid())
				{
					$data = $request->request->get('administrationbundle_phrase');

					$repP = $this->getDoctrine()->getRepository('AmbigussBundle:Phrase');
					$res = null;
					if(!empty($data['idPhrase']))
					{
						$res = $repP->findBy(array('id' => $data['idPhrase']));
					}
					else if(!empty($data['contenuPhrase']))
					{
						$res = $repP->findLike($data['contenuPhrase']);
					}
					else if(!empty($data['idAuteur']))
					{
						$res = $repP->findBy(array('auteur' => $data['idAuteur']));
					}

					return $this->json(array(
						'succes' => true,
						'phrases' => $res,
					));
				}

				return $this->render('AdministrationBundle:Administration:phrases.html.twig', array(
					'form' => $form->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function glosesAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_ADMINISTRATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$form = $this->get('form.factory')->create(SearchGloseType::class);

				$form->handleRequest($request);

				if($form->isSubmitted() && $form->isValid())
				{
					$data = $request->request->get('administrationbundle_glose');

					$repG = $this->getDoctrine()->getRepository('AmbigussBundle:Glose');
					$res = null;
					if(!empty($data['idGlose']))
					{
						$res = $repG->findBy(array('id' => $data['idGlose']));
					}
					else
					{
						if(!empty($data['contenuGlose']))
						{
							$res = $repG->findLike($data['contenuGlose']);
						}
						else
						{
							if(!empty($data['idAuteur']))
							{
								$res = $repG->findBy(array('auteur' => $data['idAuteur']));
							}
						}
					}

					return $this->json(array(
						'succes' => true,
						'gloses' => $res,
					));
				}

				$glose = new \AmbigussBundle\Entity\Glose();
				$editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
					'action' => $this->generateUrl('ambiguss_glose_edit'),
				));

				return $this->render('AdministrationBundle:Administration:gloses.html.twig', array(
					'form' => $form->createView(),
					'editGloseForm' => $editGloseForm->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}
}