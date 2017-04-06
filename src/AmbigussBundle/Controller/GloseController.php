<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 05/04/2017
 * Time: 16:05
 */

namespace AmbigussBundle\Controller;

use AmbigussBundle\Form\GloseEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GloseController extends Controller
{

	public function mainAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
				$gloses = $repo->getSignale(array(
					'signale' => true,
				));

				$glose = new \AmbigussBundle\Entity\Glose();
				$editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
					'action' => $this->generateUrl('ambiguss_glose_edit'),
				));

				return $this->render('AmbigussBundle:Glose:getAll.html.twig', array(
					'gloses' => $gloses,
					'editGloseForm' => $editGloseForm->createView(),
				));
			}
			else
			{
				if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
				{
					$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

					return $this->redirectToRoute('user_connexion');
				}
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function editGloseAction(Request $request, \AmbigussBundle\Entity\Glose $glose)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$form = $request->request->get('glose_edit');
				if(!empty($glose) && !empty($form['valeur']) && isset($form['signale']))
				{
					$glose->setValeur($form['valeur']);
					$glose->setSignale($form['signale']);
					$glose->setModificateur($this->getUser());
					$glose->setDateModification(new \DateTime());

					try
					{
						$em = $this->getDoctrine()->getManager();
						$em->persist($glose);
						$em->flush();

						$res = array(
							'valeur' => $glose->getValeur(),
							'modificateur' => $this->getUser()->getPseudo(),
							'dateModification' => $glose->getDateModification()->format('d/m/Y à H:i'),
							'signale' => $glose->getSignale(),
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
				throw $this->createNotFoundException('Les paramètres sont invalides.');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function deleteGloseAction(\AmbigussBundle\Entity\Glose $glose)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				try
				{
					$em = $this->getDoctrine()->getEntityManager();
					$em->remove($glose);
					$em->flush();

					return $this->json(array(
						'succes' => true,
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
		throw $this->createAccessDeniedException();
	}

	public function linkGloseAction(\AmbigussBundle\Entity\Glose $glose)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
				$reponses = $repo->findBy(array(
					'glose' => $glose,
				));

				return $this->json(array(
					'reponses' => count($reponses),
				));
			}
		}
		throw $this->createAccessDeniedException();
	}
}