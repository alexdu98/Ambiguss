<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\Phrase;
use AppBundle\Form\Glose\GloseEditType;
use AppBundle\Form\MAG\MAGType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ModoController extends Controller
{

	public function showGlosesAction()
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
				$gloses = $repo->getSignalees();

				$glose = new Glose();
				$editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
					'action' => $this->generateUrl('modo_glose_edit', array('id' => 0)),
				));

				return $this->render('AppBundle:Glose:getAll.html.twig', array(
					'gloses' => $gloses,
					'editGloseForm' => $editGloseForm->createView(),
				));
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('fos_user_security_login');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function editGloseAction(Request $request, Glose $glose)
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

					$repoG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
					$repoR = $this->getDoctrine()->getManager()->getRepository('AppBundle:Reponse');

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
						'modificateur' => $glose->getModificateur() != null ? $glose->getModificateur()->getUsername() : '',
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
						if($e instanceof UniqueConstraintViolationException)
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

				return $this->redirectToRoute('fos_user_security_login');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function deleteGloseAction(Glose $glose)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				try
				{
					$em = $this->getDoctrine()->getManager();
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

				return $this->redirectToRoute('fos_user_security_login');
			}
		}
		throw $this->createAccessDeniedException();
	}

	public function countReponsesAction(Glose $glose)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Reponse');
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

				return $this->redirectToRoute('fos_user_security_login');
			}
		}
		throw $this->createAccessDeniedException();
	}

    public function showPhrasesAction()
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
                $phrases = $repo->getSignalees();

                return $this->render('AppBundle:Phrase:getAll.html.twig', array(
                    'phrases' => $phrases,
                ));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

                return $this->redirectToRoute('fos_user_security_login');
            }
        }
        throw $this->createAccessDeniedException();
    }

    public function showMotsAmbigusGlosesAction(Request $request)
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
                        $repMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
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
                            $repG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
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

                return $this->render('AppBundle:MAG:get.html.twig', array(
                    'form' => $form->createView(),
                ));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

                return $this->redirectToRoute('fos_user_security_login');
            }
        }
        throw $this->createAccessDeniedException();
    }

    public function deleteMotsAmbigusGlosesAction(Request $request)
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
                        $repMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
                        $repG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');

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

                return $this->redirectToRoute('fos_user_security_login');
            }
        }
        throw $this->createAccessDeniedException();
    }

    public function unsignaleAction(Phrase $phrase)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                try
                {
                    $phrase->setSignale(0);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($phrase);
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

                return $this->redirectToRoute('fos_user_security_login');
            }
        }
        throw $this->createAccessDeniedException();
    }

    public function deleteAction(Phrase $phrase)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                try
                {
                    $phrase->setVisible(0);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($phrase);
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

                return $this->redirectToRoute('fos_user_security_login');
            }
        }
        throw $this->createAccessDeniedException();
    }

}
