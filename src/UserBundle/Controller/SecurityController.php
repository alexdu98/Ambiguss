<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
	public function connexionAction(Request $request)
	{
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('ambiguss_accueil');
		}

		$authenticationUtils = $this->get('security.authentication_utils');

		return $this->render('UserBundle:Security:connexion.html.twig', array(
			'last_username' => $authenticationUtils->getLastUsername(),
			'error'         => $authenticationUtils->getLastAuthenticationError(),
		));
	}

	public function inscriptionAction(Request $request)
	{
		return $this->render('UserBundle:Security:inscription.html.twig', array(
			'error'     => null
		));
	}
}