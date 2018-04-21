<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassementController extends Controller
{

	public function generalAction()
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
			$classement = $repository->getClassementGeneral($this->getParameter('maxResultForClassementGeneral'));
			$position = $repository->getPositionClassement($this->getUser());
			$nbMembreTotal = $repository->countEnabled();

			return $this->render('AppBundle:Classement:points.html.twig', array(
				'classement' => $classement,
				'position' => $position,
				'nbMembreTotal' => $nbMembreTotal,
			));
		}
		else
		{

			$this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");

			return $this->redirectToRoute('fos_user_security_login');
		}
	}

	public function personnelAction()
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
			$classement = $repository->getClassementPhrasesUser($this->getUser());

			return $this->render('AppBundle:Classement:phrasesUser.html.twig', array(
				'classement' => $classement,
				'dureeAvantJouabiliteSecondes' => $this->getParameter('dureeAvantJouabiliteSecondes'),
			));
		}
		else
		{

			$this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");

			return $this->redirectToRoute('fos_user_security_login');
		}
	}

	public function phrasesPersonnellesAction()
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
			$classement = $repository->getClassementPhrases($this->getParameter('maxResultForClassementPhrases'));

			return $this->render('AppBundle:Classement:phrases.html.twig', array(
				'classement' => $classement,
			));
		}
		else
		{

			$this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");

			return $this->redirectToRoute('fos_user_security_login');
		}
	}

}
