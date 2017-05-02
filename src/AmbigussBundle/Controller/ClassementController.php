<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 21/03/2017
 * Time: 09:23
 */

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassementController extends Controller{

	public function classementGeneralAction(){
		$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
		$classement = $repository->getClassementGeneral($this->getParameter('maxResultForClassementGeneral'));

		return $this->render('AmbigussBundle:Classement:points.html.twig', array (
			'classement' => $classement,
		));
	}
    public function classementPersonnelAction(){
        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
	    $classement = $repository->getClassementPhrasesUser($this->getUser());

        return $this->render('AmbigussBundle:Classement:phrasesUser.html.twig', array (
	        'classement' => $classement,
	        'dureeAvantJouabiliteSecondes' => $this->getParameter('dureeAvantJouabiliteSecondes'),
        ));
    }
    public function classementPhrasesAction(){
        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
        $classement = $repository->getClassementPhrases($this->getParameter('maxResultForClassementPhrases'));

        return $this->render('AmbigussBundle:Classement:phrases.html.twig', array (
            'classement' => $classement,
        ));
    }

}