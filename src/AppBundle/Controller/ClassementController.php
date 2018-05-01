<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassementController extends Controller
{

	public function joueursAction()
	{
        $repoMembre = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
        $classement = $repoMembre->getClassementGeneral($this->getParameter('maxResultForClassementGeneral'));
        $nbMembreTotal = $repoMembre->countEnabled();

        // Si c'est un membre, on calcul sa position dans le classement
        $position = $this->getUser() ? $repoMembre->getPositionClassement($this->getUser()) : null;

        return $this->render('AppBundle:Classement:joueurs.html.twig', array(
            'classement' => $classement,
            'position' => $position,
            'nbMembreTotal' => $nbMembreTotal,
        ));
	}

	public function phrasesAction()
	{
		if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $repoPhrase = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $classement = $repoPhrase->getClassementPhrases($this->getParameter('maxResultForClassementPhrases'));

        return $this->render('AppBundle:Classement:phrases.html.twig', array(
            'classement' => $classement,
        ));
	}

    public function personnelAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $repoPhrase = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $classement = $repoPhrase->getClassementPhrasesUser($this->getUser());

        return $this->render('AppBundle:Classement:phrasesUser.html.twig', array(
            'classement' => $classement,
            'dureeAvantJouabiliteSecondes' => $this->getParameter('dureeAvantJouabiliteSecondes'),
        ));
    }

}
