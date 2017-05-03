<?php

namespace AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}