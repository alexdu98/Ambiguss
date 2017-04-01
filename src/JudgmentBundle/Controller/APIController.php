<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 01/04/2017
 * Time: 19:37
 */

namespace JudgmentBundle\Controller;

use JudgmentBundle\Entity\Jugement;
use JudgmentBundle\Form\JugementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class APIController extends Controller
{
	public function addJugementAction(Request $request){
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$jug = new Jugement();
			$form = $this->get('form.factory')->create(JugementAddType::class, $jug, array(
				'action' => $this->generateUrl('jugement_add'),
			));

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
				$jugement = $form->getData();

				$dateDeliberation = new \DateTime();
				$jugement->setDateDeliberation($dateDeliberation->getTimestamp() + $this->getParameter('dureeDeliberationSecondes'));
				$jugement->setIdObjet(); // A remplir
				$jugement->setTypeObjet(); // A remplir
				$jugement->setAuteur($this->getUser());

				try
				{
					$em = $this->getDoctrine()->getManager();
					$em->persist($jugement);
					$em->flush();

					return $this->json(array(
						'succes' => true,
					    'action' => 'signale'
					));
				}
				catch(\Exception $e)
				{
					return $this->json(array(
						'succes' => false,
						'message' => $e,
					));
				}
			}
		}

		throw $this->createNotFoundException();
	}

}