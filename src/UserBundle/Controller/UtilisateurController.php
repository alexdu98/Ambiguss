<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UtilisateurController extends Controller
{
	public function profilAction(Request $request)
	{
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('user_connexion');
		}

		$score = $this->getUser()->getPointsClassement();
		$credit = $this->getUser()->getCredits();
		$niveau = $this->getUser()->getNiveau()->getTitre();

		return $this->render('UserBundle:Utilisateur:profil.html.twig', array(
                'score' => $score,
                'credit' => $credit,
                'niveau' => $niveau,
            ));
	}
}