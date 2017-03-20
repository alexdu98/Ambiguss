<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\MotAmbiguPhrase;
use AmbigussBundle\Form\PhraseAddType;
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
		    $form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);

		    $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();

                $phrase->setAuteur($this->getUser());

                // On trouve les mots ambigus
                $mots_ambigu = array();
                preg_match_all('#<amb id="([0-9]+)">(.*?)<\/amb>#', $data->getContenu(), $mots_ambigu, PREG_SET_ORDER);
                // Il faudra penser à refaire l'ordre !
	            /*
	             * $mots_ambigu[0] contient un array du premier match
	             * $mots_ambigu[1] contient un array du deuxieme match
	             *
	             * $mots_ambigu[][0] contient toute la balise <amb ... </amb>
	             * $mots_ambigu[][1] contient l'id / l'ordre du mot ambigu
	             * $mots_ambigu[][2] contient le mot ambigu
	             */
                $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                foreach($mots_ambigu as $mot_ambigu)
                {
	                // Soit on le trouve dans la BD soit on l'ajoute
	                $mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu[2]);

	                $map = new MotAmbiguPhrase();
	                $map->setOrdre($mot_ambigu[1]);
	                $map->setPhrase($phrase);
	                $map->setMotAmbigu($mot_ambigu_OBJ);

					$phrase->addMotsAmbigus($map);
                }

                // On a pas la glose car gloses mapped false dans MotAmbiguTypen mais si true, erreur au post
                var_dump($phrase->getMotsAmbigus()->get(3)->getMotAmbigu()->getGloses());

	            try{
		            $em = $this->getDoctrine()->getManager();
		            $em->persist($phrase);
		            $em->flush();
		            $this->get('session')->getFlashBag()->add('succes', "La phrase a bien été ajoutée");
		            // Réinitialise le formulaire
		            $phrase = new \AmbigussBundle\Entity\Phrase();
		            $form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);
	            }
	            catch(Exception $e){
		            $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase");
	            }
            }
            return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
                'form' => $form->createView(),
            ));
    	}
    	else
	    {
		    $this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");
    		return $this->redirectToRoute('user_connexion');
    	}
    }
}
