<?php

namespace AmbigussBundle\Controller;

use AmbigussBundle\Entity\AimerPhrase;
use AmbigussBundle\Entity\MotAmbigu;
use AmbigussBundle\Entity\MotAmbiguPhrase;
use AmbigussBundle\Entity\Reponse;
use AmbigussBundle\Form\GloseAddType;
use AmbigussBundle\Form\PhraseAddType;
use AmbigussBundle\Form\PhraseEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PhraseController extends Controller
{

	public function mainAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$phrase = new \AmbigussBundle\Entity\Phrase();
			$form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);

			$form->handleRequest($request);

			$newPhrase = null;
			if($form->isSubmitted() && $form->isValid())
			{
				$data = $form->getData();

				$phrase->setAuteur($this->getUser());
				$phrase->removeMotsAmbigusPhrase();

				// Normalise la phrase
				$phrase->normalize();

				// Vérifie que la phrase soit bien formée
				$res = $phrase->isValid();
				$succes = $res['succes'];

				if($this->getUser()->getCredits() < $this->getParameter('costCreatePhraseByMotAmbiguCredits') * count($res['motsAmbigus']))
				{
					$succes = false;
					$res['message'] = "Vous n'avez pas assez de crédits pour créer une phrase avec " . count($res['motsAmbigus']) . " mots ambigus.";
				}

				if($succes === true)
				{

					$mots_ambigu = $res['motsAmbigus'];

					/*
					 * $mots_ambigu[0] contient un array du premier match
					 * $mots_ambigu[1] contient un array du deuxieme match...
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
						$mot_ambigu_OBJ->setAuteur($this->getUser());
						// Normalise le mot ambigu
						$mot_ambigu_OBJ->normalize();
						$mot_ambigu_OBJ = $repository->findOneOrCreate($mot_ambigu_OBJ);

						$map = new MotAmbiguPhrase();
						$map->setOrdre($key + 1);
						$map->setPhrase($phrase);
						$map->setMotAmbigu($mot_ambigu_OBJ);

						$phrase->addMotAmbiguPhrase($map);
					}

					$phrase->getAuteur()->updateCredits(-$this->getParameter('costCreatePhraseByMotAmbiguCredits') * count($mots_ambigu));
					$phrase->getAuteur()->updatePoints($this->getParameter('gainCreatePhrasePoints'));

					$em = $this->getDoctrine()->getManager();
					$em->getConnection()->beginTransaction();
					try
					{

						$em->persist($phrase);
						$em->flush();

						$repository1 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
						$repository2 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
						$repository3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');

						$mapsRep = $request->request->get('phrase_add')['motsAmbigusPhrase'];

						foreach($phrase->getMotsAmbigusPhrase() as $map)
						{
							$rep = new Reponse();
							$rep->setContenuPhrase($phrase->getContenu());
							$rep->setValeurMotAmbigu($map->getMotAmbigu()->getValeur());
							// -1 car l'ordre commence à 1 et le reorder à 0
							$keyForMotsAmbigusPhrase = $mots_ambigu[ $map->getOrdre() - 1 ][1];
							$idGlose = $mapsRep[ $keyForMotsAmbigusPhrase ]['gloses'];
							if(empty($idGlose))
							{
								throw new \Exception("Tous les mots ambigus doivent avoir une glose");
							}
							$glose = $repository3->find($idGlose);
							$rep->setValeurGlose($glose->getValeur());
							$rep->setAuteur($this->getUser());
							$poidsReponse = $mapsRep[ $keyForMotsAmbigusPhrase ]['poidsReponse'];
							$rep->setPoidsReponse($repository1->find($poidsReponse));
							$rep->setNiveau($repository2->findOneByTitre('Facile'));
							$rep->setGlose($glose);
							$rep->setMotAmbiguPhrase($map);

							$map->addReponse($rep);

							if(!$map->getMotAmbigu()->getGloses()->contains($glose))
							{
								$map->getMotAmbigu()->addGlose($glose);
							}

							$em->persist($map);
							$em->persist($rep);

							$em->flush();
						}

						$em->getConnection()->commit();

						$newPhrase = $phrase;

						// Réinitialise le formulaire
						$phrase = new \AmbigussBundle\Entity\Phrase();
						$form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);
					}
					catch(\Exception $e)
					{
						$em->getConnection()->rollBack();
						$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $e->getMessage());
					}
				}
				else
				{
					$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $res['message']);
				}
			}

			$glose = new \AmbigussBundle\Entity\Glose();
			$addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
				'action' => $this->generateUrl('ambiguss_glose_add'),
			));

			return $this->render('AmbigussBundle:Phrase:add.html.twig', array(
				'form' => $form->createView(),
				'newPhrase' => $newPhrase,
				'addGloseForm' => $addGloseForm->createView(),
			));
		}
		else
		{

			$this->get('session')->getFlashBag()->add('erreur', "Vous devez être connecté.");

			return $this->redirectToRoute('user_connexion');
		}
	}

	public
	function likeAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
	{

		$rep = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:AimerPhrase');
		$aimerPhrase = $rep->findOneBy(array(
			'phrase' => $phrase,
			'membre' => $this->getUser(),
		));

		$action = null;
		if(!$aimerPhrase)
		{
			$aimerPhrase = new AimerPhrase();
			$aimerPhrase->setPhrase($phrase)->setMembre($this->getUser());
			// ajoute X points au créateur
			$aimerPhrase->getPhrase()->getAuteur()->updatePoints($this->getParameter('gainPerLikePhrasePoints'));
			$action = 'like';
		}
		else
		{
			if($aimerPhrase->getActive() === false)
		{
			$aimerPhrase->setActive(true);
			$action = 'relike';
		}
		else
		{
			$aimerPhrase->setActive(false);
			$action = 'unlike';
		}
		}

		$em = $this->getDoctrine()->getManager();
		$em->persist($aimerPhrase);
		$em->flush();

		return $this->json(array(
			'status' => 'succes',
			'action' => $action,
		));
	}

	public
	function SignalAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
	{
		if($phrase->getSignale() == 0)
		{
			$phrase->setSignale(1);
			$action = 'signal';
			// TODO : Créer le jugement
		}
		else
		{
			$phrase->setSignale(0);
			$action = 'cancel';
		}
		$em = $this->getDoctrine()->getManager();
		$em->persist($phrase);
		$em->flush();

		return $this->json(array(
			'status' => 'succes',
			'action' => $action,
		));

	}

    public function moderationAction()
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                $repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
                $phrases= $repo->getSignale(array(
                    'signale' => true,
                ));


                return $this->render('AmbigussBundle:Phrase:getAll.html.twig', array(
                    'phrases' => $phrases,
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

    public function moderation_maAction()
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                $repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbigu');
                $ma= $repo->getSignale(array(
                    'signale' => true,
                ));


                return $this->render('AmbigussBundle:MAmbigu:getAll.html.twig', array(
                    'mambigus' => $ma,
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


    public function keepAction(\AmbigussBundle\Entity\Phrase $phrase)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                try
                {
                    $phrase->setSignale(0);
                    $em = $this->getDoctrine()->getEntityManager();
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
        }
        throw $this->createAccessDeniedException();
    }

    public function deleteAction(\AmbigussBundle\Entity\Phrase $phrase)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                try
                {
                    $phrase->setVisible(0);
                    $em = $this->getDoctrine()->getEntityManager();
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
        }
        throw $this->createAccessDeniedException();
    }

    public function editAction(Request $request, \AmbigussBundle\Entity\Phrase $phrase)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {

                try
                {
                    $phr = new \AmbigussBundle\Entity\Phrase();
                    $nb_MA= count(preg_split("/(<a(.)+b>)/",$phrase->getContenu()));

                    // au cas ou on disable les MA
                   // $test= preg_replace("/(<a(.)+b>)/", "<span contenteditable=\"false\">\$1</span>",$phrase->getContenu());
                    $phr->setContenu($phrase->getContenu());

                    $form = $this->get('form.factory')->create(PhraseEditType::class, $phr,array(
                        'contenu'=> $phr->getContenu()));

                    $form->handleRequest($request);

                    if($form->isSubmitted() ) {
                        $data = $form->getData();
                        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
                        $phrase_modif = $repository->find($phrase->getId());
                        $phrase_modif->setModificateur($this->getUser());
                        $phrase_modif->setDateModification( new \DateTime());

                        //problème car data->getContenu() contient les modifs mais pas les anciennes
                        //balises des MA
                        $phrase_modif->setContenu($data->getContenu());

                        try {
                            /*
                            $em = $this->getDoctrine()->getManager();
                            $em->getConnection()->beginTransaction();
                            $em->persist($phrase_modif);
                            $em->flush();*/

                            // Réinitialise le formulaire
                            $phrase = new \AmbigussBundle\Entity\Phrase();
                            $form = $this->get('form.factory')->create(PhraseAddType::class, $phrase);
                        } catch (\Exception $e) {
                                $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase -> " . $e->getMessage());
                            }

                    }
                        $newPhrase = null;
                        $glose = new \AmbigussBundle\Entity\Glose();
                        $addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array(
                            'action' => $this->generateUrl('ambiguss_glose_add'),
                        ));

                    return $this->render('AmbigussBundle:Phrase:edit.html.twig', array(
                        'form' => $form->createView(),
                        'newPhrase' => $phr,
                        'nb_MA' => $nb_MA,
                        'addGloseForm' => $addGloseForm->createView(),
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
}
