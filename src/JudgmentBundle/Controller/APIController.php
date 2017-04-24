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
use UserBundle\Entity\Historique;

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

			if($form->isValid())
			{
				$jugement = $form->getData();

				$dateDeliberation = new \DateTime();
				$jugement->setDateDeliberation(\DateTime::createFromFormat('U', $dateDeliberation->getTimestamp() + $this->getParameter('dureeDeliberationSecondes')));
				$jugement->setIdObjet($request->request->get('jugement_add')['idObjet']);
				$jugement->setAuteur($this->getUser());

				$em = $this->getDoctrine()->getManager();

				// On enregistre dans l'historique du joueur
				$histJoueur = new Historique();
				$histJoueur->setMembre($this->getUser());

				$obj = null;
				if($jugement->getTypeObjet()->getTypeObjet() == 'Phrase')
				{
					$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
					$phrase = $repoP->find($jugement->getIdObjet());
					$phrase->setSignale(true);
					$obj = $phrase;
					$histJoueur->setValeur("Signalement de la phrase n°" . $phrase->getId() . ".");
				}
				else
				{
					if($jugement->getTypeObjet()->getTypeObjet() == 'Glose')
					{
						$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
						$glose = $repoP->find($jugement->getIdObjet());
						$glose->setSignale(true);
						$obj = $glose;
						$histJoueur->setValeur("Signalement de la glose n°" . $glose->getId() . ".");
					}
				}

				$em->persist($jugement);
				$em->persist($obj);
				$em->persist($histJoueur);

				try
				{
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

			return $this->json(array(
				'succes' => false,
				'message' => $form->getErrors(true),
			));
		}

		throw $this->createNotFoundException();
	}

}