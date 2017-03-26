<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 21/03/2017
 * Time: 09:23
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Form\GloseAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class APIController extends Controller{

	public function addGloseAction(Request $request){
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$glose = new \AmbigussBundle\Entity\Glose();
			$form = $this->get('form.factory')->create(GloseAddType::class, $glose);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()){
				$data = $form->getData();

				$data->setAuteur($this->getUser());

				$serializer = $this->get('fos_js_routing.serializer');

				try{
					$em = $this->getDoctrine()->getManager();
					$em->persist($data);
					$em->flush();

					/**
					 * TODO : Supprimer l'utilisateur avant de sérialisé la glose (mdp)
					 */
					return $this->json(array('status' => 'succes', 'glose' => $serializer->serialize($data, 'json')));
				}
				catch(\Exception $e){
					return $this->json(array('status' => 'erreur', 'message' => $e));
				}
			}
			return $this->render('AmbigussBundle:Glose:add.html.twig', array(
				'form' => $form->createView(),
			));
		}

		throw $this->createNotFoundException();
	}

	public function getGlosesByMotAmbiguAction(Request $request){
		$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
		$gloses = $repository->findGlosesValueByMotAmbiguValue($request->request->get('motAmbigu'));
		return $this->json($gloses);
	}

}