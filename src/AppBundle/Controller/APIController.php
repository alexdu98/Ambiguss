<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\Jugement;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Form\Jugement\JugementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Historique;

class APIController extends Controller
{

	public function addJugementAction(Request $request)
	{
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
				if($jugement->getTypeObjet()->getNom() == 'Phrase')
				{
					$repoP = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
					$phrase = $repoP->find($jugement->getIdObjet());
					$phrase->setSignale(true);
					$obj = $phrase;
					$histJoueur->setValeur("Signalement de la phrase n°" . $phrase->getId() . ".");
				}
				else
				{
					if($jugement->getTypeObjet()->getNom() == 'Glose')
					{
						$repoP = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
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
						'action' => 'signale',
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

	public function gloseAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$data = $request->request->all();

				if(!empty($data['id']) && is_numeric($data['id']))
				{
					$repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
					$repoTO = $this->getDoctrine()->getManager()->getRepository('AppBundle:TypeObjet');

					$typeObj = $repoTO->findOneBy(array('nom' => 'Glose'));
					$jugements = $repoJ->findBy(array(
						'typeObjet' => $typeObj,
						'verdict' => null,
						'idObjet' => $data['id'],
					));

					return $this->json(array(
						'succes' => true,
						'jugements' => $jugements,
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

	public function editAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
		{
			if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$data = $request->request->all();

				if($this->isCsrfTokenValid('jugement_vote', $data['token']))
				{
					if(!empty($data['id']) && is_numeric($data['id']))
					{
						$repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
						$repoTV = $this->getDoctrine()->getManager()->getRepository('AppBundle:TypeVote');

						$jugement = $repoJ->find($data['id']);
						$jugement->setDateDeliberation(new \DateTime());
						$jugement->setJuge($this->getUser());
						$jugement->setVerdict($repoTV->findOneBy(array('nom' => $data['verdict'])));

						// On enregistre dans l'historique du joueur
						$histJoueur = new Historique();
						$histJoueur->setMembre($jugement->getAuteur());
						$histJoueur->setValeur("Jugement n°" . $jugement->getId() . ", verdict : " . $jugement->getVerdict()->getNom() . ".");

						$em = $this->getDoctrine()->getManager();
						$em->persist($jugement);
						$em->persist($histJoueur);

						try
						{
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
			}
			else
			{
				$this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

				return $this->redirectToRoute('fos_user_security_login');
			}
		}
		throw $this->createAccessDeniedException();
	}

    public function autocompleteGloseAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
        $gloses = $repository->findByValeurAutoComplete($request->get('term'));

        return $this->json($gloses);
    }

    public function autocompleteMotAmbiguAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
        $motsAmbigus = $repository->findByValeurAutoComplete($request->get('term'));

        return $this->json($motsAmbigus);
    }

    public function addGloseAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $glose = new Glose();
            $form = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
                'action' => $this->generateUrl('ambiguss_glose_add'),
            ));

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $glose = $form->getData();

                $glose->setAuteur($this->getUser());

                // Normalise la glose
                $glose->normalize();

                $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
                $repoMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');

                $ma = $repoMA->findOneBy(array('valeur' => $request->request->get('glose_add')['motAmbigu']));
                // 2 premières gratuites
                $nbGloses = !empty($ma) && $ma->getGloses()->count() >= 2 ? $ma->getGloses()->count() : 0;

                $cout = -($nbGloses * $this->getParameter('costCreateGloseByGlosesOfMotAmbigu'));
                $this->getUser()->updateCredits($cout);

                $em = $this->getDoctrine()->getManager();

                $glose = $repository->findOneBy(array('valeur' => $glose->getValeur()));
                if($glose == null){
                    $em->persist($glose);
                    $em->flush();
                }

                $motAmbigu = new MotAmbigu();
                $motAmbigu->setValeur($request->request->get('glose_add')['motAmbigu']);
                $motAmbigu->setAuteur($this->getUser());

                $tmp = $repoMA->findOneBy(array('valeur' => $motAmbigu->getValeur()));
                if($tmp == null){
                    $em->persist($motAmbigu);
                    $em->flush();
                }
                else
                    $motAmbigu = $tmp;

                $motAmbigu->addGlose($glose);

                $em->persist($motAmbigu);
                $em->persist($this->getUser());

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
                        'succes' => true,
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
                        'succes' => true,
                        'liaisonExiste' => true,
                        'glose' => $res,
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

    public function getGlosesByMotAmbiguAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
        $gloses = $repository->findGlosesValueByMotAmbiguValue($request->request->get('motAmbigu'));

        return $this->json($gloses);
    }

}
