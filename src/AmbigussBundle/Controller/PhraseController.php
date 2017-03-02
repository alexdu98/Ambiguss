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
            //créer l'objet mot_ambigu
            $mot_ambigu = new \AmbigussBundle\Entity\MotAmbigu();
			//créer l'objet glose
			$glose = new \AmbigussBundle\Entity\Glose();

			$formMotBuilder = $this->get('form.factory')->createBuilder(MotAmbiguPhraseType::class, $mot_ambigu_phrase)
            ->add('Valider', SubmitType::class);
			$formMot = $formMotBuilder->getForm();

            return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
                'formMot'=> $formMot->createView(),
            ));




            if ($request->isMethod('POST')) {
                $formMot->handleRequest($request);
                if ($formMot->isValid()) {
                    // Ordre d'ajout : phrase -> motAmbigu -> mot_ambigu_phrase  -> les gloses associé au mot ambigu

                    //recup de l'utilisateur courant
                    $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
                    $m = $repository->find($this->get('security.token_storage')->getToken()->getUser()->getId());
                    $phrase->setAuteur($m);
                    try{
                        // On enregistre la phrase dans la base de données
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($phrase);
                        $em->flush();
                    }
                    catch(Exception $e){
                        $this->get('session')->setFlash('erreur', "Erreur lors de l'insertion de la phrase");
                    }

                    //A FAIRE Si le mot ambigu phrase n'est pas déjà dans la bdd il faut créer un mot ambigu, sinon il faut chercher l'id du mot ambigu déjà présent 
                    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                    $m = $repository->findOneBy(array('valeur' => $mot_ambigu));
                    if ($m==null) {
                        $m=$mot_ambigu;
                        try {
                            // On enregistre le mot ambigu dans la base de données
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($mot_ambigu);
                            $em->flush();
                        } catch (Exception $e) {
                            $this->get('session')->setFlash('erreur', "Erreur lors de l'insertion du mot ambigu");
                        }
                        // Une fois qu'on a le mot ambigu on peut le lier au mot ambigu phrase et à la phrase
                        $mot_ambigu_phrase->setMotAmbigu($mot_ambigu->getId());
                    }
                    else{
                        $mot_ambigu_phrase->setMotAmbigu($m->getId());
                    }

                    $mot_ambigu_phrase->setPhrase($phrase->getId());
                    try{
                        // On enregistre le mot ambigu phrase dans la base de données
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($mot_ambigu_phrase);
                        $em->flush();
                    }
                    catch(Exception $e){
                        $this->get('session')->setFlash('erreur', "Erreur lors de l'insertion du mot ambigu phrase");
                    }

                    //A FAIRE On récupére les gloses pour les lié au mot ambigu

                }

            }

    	}
    	else{
    		return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
    			'pasConnect' => "Vous n'êtes pas connecté." 
    			));
    	}
    }
}
