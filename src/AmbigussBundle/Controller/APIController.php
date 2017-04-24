<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 21/03/2017
 * Time: 09:23
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\MotAmbigu;
use AmbigussBundle\Form\GloseAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Historique;

class APIController extends Controller
{

	public function autocompleteGloseAction(Request $request)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
		$gloses = $repository->findByValeurAutoComplete($request->get('term'));

		return $this->json($gloses);
	}

	public function addGloseAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$glose = new \AmbigussBundle\Entity\Glose();
			$form = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
				'action' => $this->generateUrl('ambiguss_glose_add'),
			));

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
				$data = $form->getData();

				$data->setAuteur($this->getUser());

				// Normalise la glose
				$data->normalize();

				$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
				$glose = $repository->findOneOrCreate($data);

				$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
				$motAmbigu = new MotAmbigu();
				$motAmbigu->setValeur($request->request->get('glose_add')['motAmbigu']);
				$motAmbigu->setAuteur($this->getUser());
				$motAmbigu = $repository->findOneOrCreate($motAmbigu);

				$motAmbigu->addGlose($glose);

				$em = $this->getDoctrine()->getManager();

				$em->persist($motAmbigu);

				try
				{
					$em->flush();

					// On enregistre dans l'historique du joueur
					$histJoueur = new Historique();
					$histJoueur->setMembre($this->getUser());
					$histJoueur->setValeur("Liaison de la glose n°" . $glose->getId() . " avec le mot ambigu n°" . $motAmbigu->getId() . ".");

					$em->persist($histJoueur);
					$em->flush();

					$res = array(
						'id' => $glose->getId(),
						'valeur' => $glose->getValeur(),
					);

					return $this->json(array(
						'status' => 'succes',
						'glose' => $res,
					));
				}
				// Si la liaison motAmbigu-glose est déjà faite
				catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e)
				{
					$res = array(
						'id' => $glose->getId(),
						'valeur' => $glose->getValeur(),
					);

					return $this->json(array(
						'status' => 'succes',
						'glose' => $res,
					));
				}
				catch(\Exception $e)
				{
					return $this->json(array(
						'status' => 'erreur',
						'message' => $e,
					));
				}
			}
		}

		throw $this->createNotFoundException();
	}

	public function getGlosesByMotAmbiguAction(Request $request)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
		$gloses = $repository->findGlosesValueByMotAmbiguValue($request->request->get('motAmbigu'));

		return $this->json($gloses);
	}

}