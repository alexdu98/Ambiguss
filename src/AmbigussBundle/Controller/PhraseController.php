<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Form\PhraseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class PhraseController extends Controller
{
    public function mainAction(Request $request)
    {
    	if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
    	{
			$phrase = new \AmbigussBundle\Entity\Phrase();
		    $form = $this->get('form.factory')->create(PhraseType::class, $phrase);

            if ($request->isMethod('POST'))
            {
	            $form->handleRequest($request);
                $data = $form->getData();
                $phrase->setAuteur($this->getUser());

                // On trouve les mots ambigus
                $mots_ambigu = array();
                preg_match_all('#<amb>(.*?)<\/amb>#', $data->getContenu(), $mots_ambigu);

                $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                foreach($mots_ambigu[1] as $mot_ambigu)
                {
	                // Soit on le trouve dans la BD soit on l'ajoute
	                $mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu);
	                // On ajoute le mot ambigu à la phrase
					$phrase->addMotAmbigu($mot_ambigu_OBJ);
                }

	            try{
		            $em = $this->getDoctrine()->getManager();
		            $em->persist($phrase);
		            $em->flush();
		            $this->get('session')->getFlashBag()->add('succes', "La phrase a bien été ajoutée");
		            // Réinitialise le formulaire
		            $phrase = new \AmbigussBundle\Entity\Phrase();
		            $form = $this->get('form.factory')->create(PhraseType::class, $phrase);
	            }
	            catch(Exception $e){
		            $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase");
	            }
            }
            return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
                'form' => $form->createView(),
                ''
            ));
    	}
    	else
	    {
		    $this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");
    		return $this->redirectToRoute('user_connexion');
    	}
    }
}
