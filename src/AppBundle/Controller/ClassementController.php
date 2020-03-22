<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClassementController extends Controller
{

	public function joueursAction(Request $request)
	{
        $repoMembre = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
        $type = $request->query->get('type');
        $typeClassement = in_array($type, ['mensuel', 'hebdomadaire']) ? $type : 'général';
        $maxResult = $this->getParameter('maxResultForClassementGeneral');

        $classement = $repoMembre->getClassement($typeClassement, $maxResult);
        $nbMembreTotal = $repoMembre->countClassement($typeClassement);

        // Si c'est un membre, on calcul sa position dans le classement
        $position = $this->getUser() ? $repoMembre->getPositionClassement($typeClassement, $this->getUser()) : null;

        return $this->render('AppBundle:Classement:joueurs.html.twig', array(
            'classement' => $classement,
            'position' => $position,
            'nbMembreTotal' => $nbMembreTotal,
            'typeClassement' => $typeClassement
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
