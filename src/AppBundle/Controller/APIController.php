<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\JAime;
use AppBundle\Entity\Jugement;
use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\Phrase;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Form\Jugement\JugementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Historique;

class APIController extends Controller
{

	public function newJugementAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$jugement = new Jugement();
			$form = $this->get('form.factory')->create(JugementAddType::class, $jugement);

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
                $em = $this->getDoctrine()->getManager();
				$jugement = $form->getData();
				$dureeDeliberation = $this->getParameter('dureeDeliberationSecondes');

				// Calcul la date de délibération automatique
				$dateDeliberation = new \DateTime();
				$dateDeliberation = \DateTime::createFromFormat('U', $dateDeliberation->getTimestamp() + $dureeDeliberation);

				$jugement->setDateDeliberation($dateDeliberation);
				$jugement->setObjetId($request->request->get('jugement_add')['objetId']);
				$jugement->setAuteur($this->getUser());

				$histMsg = null;
                $repo = null;
				if($jugement->getTypeObjet()->getNom() == 'Phrase') {
					$repo = $em->getRepository('AppBundle:Phrase');
                    $histMsg = "Signalement de la phrase n°" . $jugement->getObjetId() . ".";
				}
				elseif($jugement->getTypeObjet()->getNom() == 'Glose') {
                    $repo = $em->getRepository('AppBundle:Glose');
                    $histMsg = "Signalement de la glose n°" . $jugement->getObjetId() . ".";
				}
                elseif($jugement->getTypeObjet()->getNom() == 'Membre') {
                    $repo = $em->getRepository('AppBundle:Membre');
                    $histMsg = "Signalement du membre n°" . $jugement->getObjetId() . ".";
                }

                $obj = $repo->find($jugement->getObjetId());
                $obj->setSignale(true);

                // On enregistre dans l'historique du joueur
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
                $historiqueService->save($this->getUser(), $histMsg);

				$em->persist($jugement);
				$em->persist($obj);

                $em->flush();

                return $this->json(array(
                    'succes' => true,
                    'action' => 'signale',
                ));
			}
		}

		throw $this->createNotFoundException();
	}

    public function autocompleteGloseAction(Request $request)
    {
        $specChar = array('%', '_');
        $term = str_replace($specChar, '', $request->get('term'));

        $gloses = null;
        if (mb_strlen($term) > 1) {
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
            $gloses = $repository->findByValeurAutoComplete($term);


        }

        return $this->json($gloses);
    }

    public function autocompleteMotAmbiguAction(Request $request)
    {
        $specChar = array('%', '_');
        $term = str_replace($specChar, '', $request->get('term'));

        $motsAmbigus = null;
        if (mb_strlen($term) > 1) {
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
            $motsAmbigus = $repository->findByValeurAutoComplete($term);
        }

        return $this->json($motsAmbigus);
    }

    public function newGloseAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $succes = true;
            $message = null;
            $glose = new Glose();
            $form = $this->createForm(GloseAddType::class, $glose);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $glose = $form->getData();
                $coutNewGlose = $this->getParameter('costCreateGloseByGlosesOfMotAmbigu');

                $glose->setAuteur($this->getUser());

                // Normalise la glose
                $glose->normalize();

                $repoG = $em->getRepository('AppBundle:Glose');
                $repoMA = $em->getRepository('AppBundle:MotAmbigu');

                $g = $repoG->findOneBy(array('valeur' => $glose->getValeur()));
                if($g)
                    $glose = $g;

                // Si le mot ambigu n'existe pas on le créé (cas création/modification de phrase)
                $motAmbigu = $repoMA->findOneBy(array('valeur' => $request->request->get('glose_add')['motAmbigu']));
                $isLinked = false;
                if(!$motAmbigu){
                    $motAmbigu = new MotAmbigu();
                    $motAmbigu->setValeur($request->request->get('glose_add')['motAmbigu']);
                    $motAmbigu->setAuteur($this->getUser());
                }
                else{
                    foreach ($motAmbigu->getGloses() as $g) {
                        if ($g->getValeur() == $glose->getValeur()) {
                            $isLinked = true;
                            break;
                        }
                    }
                }

                // Si la liaison MA-G n'existait pas déjà on la créé
                if (!$isLinked) {
                    $motAmbigu->addGlose($glose);
                }

                // Les 2 premières gloses d'un mot ambigu sont gratuites
                $nbGloses = $motAmbigu && $motAmbigu->getGloses()->count() >= 2 ? $motAmbigu->getGloses()->count() : 0;

                // On débite les crédits
                $cout = -($nbGloses * $coutNewGlose);

                if ($this->getUser()->getCredits() >= $cout) {
                    $em->getConnection()->beginTransaction();

                    $em->persist($motAmbigu);
                    $em->persist($glose);
                    $em->flush();

                    $this->getUser()->updateCredits($cout);

                    // On enregistre dans l'historique du joueur
                    $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
                    $historiqueService->save($this->getUser(), "Liaison de la glose n°" . $glose->getId() . " avec le mot ambigu n°" . $motAmbigu->getId() . ".");

                    $em->persist($this->getUser());

                    $em->flush();
                    $em->getConnection()->commit();
                }
                else {
                    $message = 'Vous n\'avez pas assez de crédits';
                    $succes = false;
                }

                $motAmbiguInfos = array(
                    'id' => $motAmbigu->getId(),
                    'valeur' => $motAmbigu->getValeur(),
                );
                $gloseInfos = array(
                    'id' => $glose->getId(),
                    'valeur' => $glose->getValeur(),
                );

                return $this->json(array(
                    'succes' => $succes,
                    'motAmbigu' => $motAmbiguInfos,
                    'glose' => $gloseInfos,
                    'liaisonExiste' => $isLinked,
                    'credits' => $this->getUser()->getCredits(),
                    'message' => $message
                ));
            }
        }

        throw $this->createNotFoundException();
    }

    public function showGlosesMotAmbiguAction(Request $request)
    {
        $repoG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
        $repoMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');

        $MA = $repoMA->findOneBy(array('valeur' => $request->request->get('motAmbigu')));
        $gloses = $repoG->findGlosesValueByMotAmbiguValue($request->request->get('motAmbigu'));

        $res = array(
            'ownerId' => $MA ? $MA->getId() : null,
            'links' => $gloses
        );

        return $this->json($res);
    }

    public function showMotsAmbigusGloseAction(Request $request)
    {
        $repoMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
        $repoG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');

        $G = $repoG->findOneBy(array('valeur' => $request->request->get('glose')));
        $MA = $repoMA->findMotsAmbigusByValueGloseValue($request->request->get('glose'));

        $res = array(
            'ownerId' => $G ? $G->getId() : null,
            'links' => $MA
        );

        return $this->json($res);
    }

    public function likeAction(Phrase $phrase)
    {
        $jaimeRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:JAime');
        $jaime = $jaimeRepo->findOneBy(array(
            'phrase' => $phrase,
            'membre' => $this->getUser(),
        ));

        $em = $this->getDoctrine()->getManager();

        $action = null;
        if(!$jaime)
        {
            $jaime = new JAime();
            $jaime->setPhrase($phrase)->setMembre($this->getUser());
            // Ajoute X points au créateur
            $jaime->getPhrase()->getAuteur()->updatePoints($this->getParameter('gainPerLikePhrasePoints'));
            $action = 'like';

            $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

            // On enregistre dans l'historique du joueur
            $historiqueService->save($this->getUser(), "Aime la phrase n°" . $phrase->getId() . ".");

            // On enregistre dans l'historique du créateur de la phrase
            $historiqueService->save(
                $phrase->getAuteur(),
                "Un joueur a aimé votre phrase n°" . $phrase->getId() . " (+" . $this->getParameter('gainPerLikePhrasePoints') . " points)."
            );
        }
        else
        {
            if($jaime->getActive() === false)
            {
                $jaime->setActive(true);
                $action = 'relike';
            }
            else
            {
                $jaime->setActive(false);
                $action = 'unlike';
            }
        }

        $em->persist($jaime);
        $em->flush();

        return $this->json(array(
            'status' => 'succes',
            'action' => $action,
        ));
    }

    public function historiqueAction(Request $request)
    {
        $draw = intval($request->query->get('draw'));
        $start = $request->query->get('start');
        $length = $request->query->get('length');
        $search = $request->query->get('search');
        $orders = $request->query->get('order');
        $columns = $request->query->get('columns');

        $historiqueRepo = $this->getDoctrine()->getRepository('AppBundle:Historique');

        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        $otherConditions = array('membre' => $this->getUser());

        $results = $historiqueRepo->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions);

        $total_objects_count = $historiqueRepo->countAllByMembre($this->getUser());

        foreach ($results['results'] as $key => $result){
            $results['results'][$key] = array(
                $result['dateAction']->format('d/m/Y H:i'), $result['valeur']
            );
        }

        $response = array(
            'draw' => $draw,
            'recordsTotal' => $total_objects_count,
            'recordsFiltered' => $results["countResult"],
            'data' => $results['results']
        );

        return $this->json($response);
    }

}
