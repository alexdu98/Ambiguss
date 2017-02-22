<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Form\MotAmbiguPhraseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class CreationPhraseController extends Controller
{
    /**
     * @Route("/")
     */
    public function mainAction(Request $request)
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

    		//créer l'objet phrase
			$phrase = new \AmbigussBundle\Entity\Phrase();
			//créer l'objet mot_ambigu_phrase
			$mot_ambigu_phrase = new \AmbigussBundle\Entity\MotAmbiguPhrase();
			//créer l'objet glose
			$glose = new \AmbigussBundle\Entity\Glose();


			//$formPhraseBuilder = $this->get('form.factory')->createBuilder(PhraseType::class, $phrase);
			/*$formPhraseBuilder->add('contenu', TextType::class, array('label' => "Entrez votre phrase"))
								->add('motAmbiguPhrase', MotAmbiguPhraseType::class, array('label' => "Quel est le mot ou l'expression ambigu dans cette phrase ?"));*/

			$formMotBuilder = $this->get('form.factory')->createBuilder(MotAmbiguPhraseType::class, $mot_ambigu_phrase);/*
			$formMotBuilder->add('valeurMotAmbigu', TextType::class, array('label' => "Dans cette phrase quel est le mot ambigu selon vous ?"))
							->add('Valider cette premiére étape', SubmitType::class);*/

			//$formPhrase = $formPhraseBuilder->getForm();
			$formMot = $formMotBuilder->getForm();



        	return $this->render('AmbigussBundle:Default:creationPhrase.html.twig', array(
      			//'formPhrase' => $formPhrase->createView(),
      			'formMot'=> $formMot->createView(),
    		));
    	}
    	else{
    		return $this->render('AmbigussBundle:Default:creationPhrase.html.twig', array(
    			'pasConnect' => "Vous n'êtes pas connecté." 
    			));
    	}
    }
}
