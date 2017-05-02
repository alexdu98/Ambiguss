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
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
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

					$repoG = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
					$repoR = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');

					// Récupère la glose avec la valeur que l'on veut insert
					$gloseM = $repoG->findOneBy(array(
						'valeur' => $glose->getValeur(),
					));

					$em = $this->getDoctrine()->getManager();

					// Si la nouvelle valeur de la glose existe déjà et que ce n'est pas la même glose
					if(!empty($gloseM) && $gloseM->getId() != $glose->getId())
					{
						// Récupère les réponses avec la glose en cours de modification
						$reponses = $repoR->findBy(array(
							'glose' => $glose,
						));

						// Modifie les réponses avec la glose en cours de modification par celle qui existe déjà
						foreach($reponses as $reponse)
						{
							$reponse->setGlose($gloseM);
							$em->persist($reponse);
						}

						// Ajoute les liaisons motAmbigu-Glose de la glose que l'on modifie à celle qui existe déjà
						$maSave = array();
						$i = 0;// Enregistre les objets pour le persist
						foreach($glose->getMotsAmbigus() as $motAmbigu)
						{
							// Si la glose qui existe déjà, n'est pas liée au mot ambigu
							if(!$gloseM->getMotsAmbigus()->contains($motAmbigu))
							{
								$maSave[ $i ] = $motAmbigu;
								$motAmbigu->addGlose($gloseM);
								$em->persist($maSave[ $i ]);
							}
						}

						// Supprime la glose
						$em->remove($glose);
					}
					else
					{
						$em->persist($glose);
					}

					if(!empty($gloseM))
					{
						$glose = $gloseM;
					}

					$res = array(
						'id' => $glose->getId(),
						'valeur' => $glose->getValeur(),
						'modificateur' => $glose->getModificateur() != null ? $glose->getModificateur()->getPseudo() : '',
						'dateModification' => $glose->getDateModification() != null ? $glose->getDateModification()->format('d/m/Y à H:i') : '',
						'signale' => $glose->getSignale(),
					);

					try
					{
						$em->flush();

						return $this->json(array(
							'succes' => true,
							'glose' => $res,
						));
					}
					catch(\Exception $e)
					{
						// Si la liaison motAmbigu-glose est déjà faite
						if($e instanceof \Doctrine\DBAL\Exception\UniqueConstraintViolationException)
						{
							return $this->json(array(
								'succes' => true,
								'glose' => $res,
							));
						}
						else
						{
							return $this->json(array(
								'succes' => false,
								'message' => $e->getMessage(),
							));
						}
					}
				}
				throw $this->createNotFoundException('Les paramètres sont invalides.');
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
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
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
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
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('user_connexion');
			}
		}
		throw $this->createAccessDeniedException();
	}
}