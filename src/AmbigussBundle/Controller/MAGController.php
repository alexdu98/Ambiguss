<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 30/04/2017
 * Time: 11:00
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Form\MAGType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MAGController extends Controller
{

	public function getAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$form = $this->get('form.factory')->create(MAGType::class);

				$form->handleRequest($request);

				if($form->isSubmitted() && $form->isValid())
				{
					$data = $form->getData();

					$succes = false;
					$res = null;
					$owner = null;
					if($form->get('rechercherMA')->isClicked())
					{
						$repMA = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
						$ma = $repMA->findOneBy(array('valeur' => $data['motAmbigu']));
						$owner = array(
							'type' => 'ma',
							'id' => $ma->getId(),
						);
						if($ma)
						{
							$succes = true;
							foreach($ma->getGloses() as $glose)
							{
								$res[] = array(
									'id' => $glose->getId(),
									'valeur' => $glose->getValeur(),
								);
							}
						}
						else
						{
							$res = 'Mot ambigu inconnu';
						}
					}
					else
					{
						if($form->get('rechercherG')->isClicked())
						{
							$repG = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
							$g = $repG->findOneBy(array('valeur' => $data['glose']));
							$owner = array(
								'type' => 'g',
								'id' => $g->getId(),
							);
							if($g)
							{
								$succes = true;
								foreach($g->getMotsAmbigus() as $motsAmbigus)
								{
									$res[] = array(
										'id' => $motsAmbigus->getId(),
										'valeur' => $motsAmbigus->getValeur(),
									);
								}
							}
							else
							{
								$res = 'Glose inconnue';
							}
						}
						else
						{
							$res = 'Recherche par mot ambigu ou glose';
						}
					}

					return $this->json(array(
						'succes' => $succes,
						'owner' => $owner,
						'data' => $res,
					));
				}

				return $this->render('AmbigussBundle:MAG:get.html.twig', array(
					'form' => $form->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function deleteAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$succes = false;
				$message = null;

				$data = $request->request->all();
				if(!empty($data['token']) && !empty($data['motAmbigu']) && !empty($data['glose']))
				{
					if($this->isCsrfTokenValid('delete_mag', $data['token']))
					{
						$repMA = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
						$repG = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');

						$ma = $repMA->find($data['motAmbigu']);
						$g = $repG->find($data['glose']);

						$ma->removeGlose($g);

						$em = $this->getDoctrine()->getManager();
						$em->persist($ma);

						try
						{
							$em->flush();

							$succes = true;
						}
						catch(\Exception $e)
						{
							$message = 'Erreur BD';
						}
					}
					else
					{
						$message = "Erreur token";
					}
				}
				else
				{
					$message = "Tous les champs ne sont pas remplis";
				}

				return $this->json(array(
					'succes' => $succes,
					'message' => $message,
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}
}