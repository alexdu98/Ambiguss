<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\MotAmbigu;
use AmbigussBundle\Entity\MotAmbiguPhrase;
use AmbigussBundle\Entity\Reponse;
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
                $phrase->removeMotsAmbigusPhrase();

                // On trouve les mots ambigus
                $mots_ambigu = array();
                preg_match_all('#<amb id="([0-9]+)">(.*?)<\/amb>#', $data->getContenu(), $mots_ambigu, PREG_SET_ORDER);

	            /*
	             * $mots_ambigu[0] contient un array du premier match
	             * $mots_ambigu[1] contient un array du deuxieme match
	             *
	             * $mots_ambigu[][0] contient toute la balise <amb ... </amb>
	             * $mots_ambigu[][1] contient l'id / l'ordre du mot ambigu
	             * $mots_ambigu[][2] contient le mot ambigu
	             */
                $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                foreach($mots_ambigu as $key => $mot_ambigu)
                {
	                // Soit on le trouve dans la BD soit on l'ajoute
	                $mot_ambigu_OBJ = new MotAmbigu();
	                $mot_ambigu_OBJ->setValeur($mot_ambigu[2]);
	                $mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu_OBJ);
	                $phrase->setContenu(preg_replace('#<amb id="'.$mot_ambigu[1].'">(.*?)<\/amb>#', '<amb id="'.($key+1)
	                                                                                                             .'">$1</amb>',
		                $phrase->getContenu()));

	                $map = new MotAmbiguPhrase();
	                $map->setOrdre($key + 1);
	                $map->setPhrase($phrase);
	                $map->setMotAmbigu($mot_ambigu_OBJ);

					$phrase->addMotAmbiguPhrase($map);
                }

	            try{
		            $em = $this->getDoctrine()->getManager();
		            $em->persist($phrase);
		            $em->flush();

		            $repository1 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
		            $repository2 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
		            $repository3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');

		            $reorder = array_values($request->request->get('phrase_add')['motsAmbigusPhrase']);

		            foreach($phrase->getMotsAmbigusPhrase() as $map){
		            	$rep = new Reponse();
		            	$rep->setContenuPhrase($phrase->getContenu());
		            	$rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
		            	// -1 car l'ordre commence à 1 et le reorder à 0
		            	$glose = $repository3->find($reorder[$map->getOrdre() - 1]['gloses']);
		            	$rep->setValeurGlose($glose->getValeur());
		            	$rep->setAuteur($this->getUser());
			            $rep->setPoidsReponse($repository1->find($reorder[$map->getOrdre() - 1]['poidsReponse']));
			            $rep->setNiveau($repository2->findOneByTitre('Facile'));
			            $rep->setGlose($glose);
			            $rep->setMotAmbiguPhrase($map);

			            if(!$map->getMotAmbigu()->getGloses()->contains($glose))
			                $map->getMotAmbigu()->addGlose($glose);

			            $em->persist($map);
			            $em->persist($rep);

			            $em->flush();
		            }

		            $this->get('session')->getFlashBag()->add('succes', "La phrase a bien été ajoutée");
		            // Réinitialise le formulaire
		            $phrase = new \AmbigussBundle\Entity\Phrase();
		            $form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);
	            }
	            catch(\Exception $e){
		            $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase" . $e);
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
