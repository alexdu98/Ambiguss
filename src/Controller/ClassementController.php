<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClassementController extends AbstractController
{

	public function joueurs(Request $request)
	{
        $repoMembre = $this->getDoctrine()->getManager()->getRepository('App:Membre');
        $type = $request->query->get('type');
        $typeClassement = in_array($type, ['mensuel', 'hebdomadaire']) ? $type : 'général';
        $maxResult = $this->getParameter('maxResultForClassementGeneral');

        $classement = $repoMembre->getClassement($typeClassement, $maxResult);
        $nbMembreTotal = $repoMembre->countClassement($typeClassement);

        // Si c'est un membre, on calcul sa position dans le classement
        $position = $this->getUser() ? $repoMembre->getPositionClassement($typeClassement, $this->getUser()) : null;

        return $this->render('App:Classement:joueurs.html.twig', array(
            'classement' => $classement,
            'position' => $position,
            'nbMembreTotal' => $nbMembreTotal,
            'typeClassement' => $typeClassement
        ));
	}

	public function phrases()
	{
		if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $repoPhrase = $this->getDoctrine()->getManager()->getRepository('App:Phrase');
        $classement = $repoPhrase->getClassementPhrases($this->getParameter('maxResultForClassementPhrases'));

        return $this->render('App:Classement:phrases.html.twig', array(
            'classement' => $classement,
        ));
	}

    public function personnel()
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $repoPhrase = $this->getDoctrine()->getManager()->getRepository('App:Phrase');
        $classement = $repoPhrase->getClassementPhrasesUser($this->getUser());

        return $this->render('App:Classement:phrasesUser.html.twig', array(
            'classement' => $classement,
            'dureeAvantJouabiliteSecondes' => $this->getParameter('app.dureeAvantJouabiliteSecondes'),
        ));
    }

}
