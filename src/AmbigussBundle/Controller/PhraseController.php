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

class PhraseController extends Controller
{
    public function mainAction(Request $request)
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

    		//créer l'objet phrase
			$phrase = new \AmbigussBundle\Entity\Phrase();
			//créer l'objet mot_ambigu_phrase
			$mot_ambigu_phrase = new \AmbigussBundle\Entity\MotAmbiguPhrase();
			//créer l'objet glose
			$glose = new \AmbigussBundle\Entity\Glose();

			$formMotBuilder = $this->get('form.factory')->createBuilder(MotAmbiguPhraseType::class, $mot_ambigu_phrase)->add('Valider', SubmitType::class);
			$formMot = $formMotBuilder->getForm();



        	return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
      			'formMot'=> $formMot->createView(),
    		));
    	}
    	else{
    		return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
    			'pasConnect' => "Vous n'êtes pas connecté." 
    			));
    	}
    }
}
