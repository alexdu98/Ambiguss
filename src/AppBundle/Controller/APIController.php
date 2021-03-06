<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\JAime;
use AppBundle\Entity\Signalement;
use AppBundle\Entity\Membre;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\Phrase;
use AppBundle\Event\AmbigussEvents;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Form\Signalement\SignalementAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Historique;

class APIController extends Controller
{

	public function newSignalementAction(Request $request)
	{
		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
		{
			$signalement = new Signalement();
			$form = $this->get('form.factory')->create(SignalementAddType::class, $signalement);

			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid())
			{
                $em = $this->getDoctrine()->getManager();
				$signalement = $form->getData();

                $objetId = $request->request->get('signalement_add')['objetId'];

                $signalement->setObjetId($objetId);
                $signalement->setAuteur($this->getUser());

                $histMsg = null;
                $repo = null;
                if($signalement->getTypeObjet()->getNom() == 'Phrase') {
                    $repo = $em->getRepository('AppBundle:Phrase');
                    $histMsg = "Signalement de la phrase n°" . $signalement->getObjetId() . ".";
                }
                elseif($signalement->getTypeObjet()->getNom() == 'Glose') {
                    $repo = $em->getRepository('AppBundle:Glose');
                    $histMsg = "Signalement de la glose n°" . $signalement->getObjetId() . ".";
                }
                elseif($signalement->getTypeObjet()->getNom() == 'Membre') {
                    $repo = $em->getRepository('AppBundle:Membre');
                    $histMsg = "Signalement du membre n°" . $signalement->getObjetId() . ".";
                }
                else {
                    return $this->json(array(
                        'succes' => false,
                        'message' => 'Type d\'objet inconnu',
                    ));
                }

                $signalementRepo = $em->getRepository('AppBundle:Signalement');
                $signalementObjUserActif = $signalementRepo->findBy(array(
                    'auteur' => $this->getUser(),
                    'objetId' => $objetId,
                    'typeObjet' => $signalement->getTypeObjet(),
                    'verdict' => null
                ));

                if($signalementObjUserActif) {
                    return $this->json(array(
                        'succes' => false,
                        'message' => 'Vous avez déjà signalé cet élément et le verdict n\'a pas encore été rendu.',
                    ));
                }

                $obj = $repo->find($signalement->getObjetId());
                $obj->setSignale(true);

                // On enregistre dans l'historique du joueur
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');
                $historiqueService->save($this->getUser(), $histMsg);

				$em->persist($signalement);
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

                $gloseService = $this->get('AppBundle\Service\GloseService');
                $nbGlosesActuelles = $motAmbigu->getGloses()->count() > 0 ? $motAmbigu->getGloses()->count() - 1 : 0;

                if ($gloseService->isCreatable($nbGlosesActuelles, $this->getUser()->getCredits())) {
                    $em->getConnection()->beginTransaction();

                    $em->persist($motAmbigu);
                    $em->persist($glose);
                    $em->flush();

                    // On débite les crédits
                    $cout = -$gloseService->getCostCreate($nbGlosesActuelles);
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
        $ed = $this->get('event_dispatcher');

        $action = null;
        if(!$jaime)
        {
            $jaime = new JAime();
            $jaime->setPhrase($phrase)->setMembre($this->getUser());

            $em->persist($jaime);
            $em->flush();

            if ($this->getUser()->getId() != $phrase->getAuteur()->getId()) {

                // Ajoute X points au créateur
                $jaime->getPhrase()->getAuteur()->updatePoints($this->getParameter('gainPerLikePhrasePoints'));

                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

                // On enregistre dans l'historique du joueur
                $historiqueService->save($this->getUser(), "Aime la phrase n°" . $phrase->getId() . ".");

                // On enregistre dans l'historique du créateur de la phrase
                $historiqueService->save(
                    $phrase->getAuteur(),
                    "Un joueur a aimé votre phrase n°" . $phrase->getId() . " (+" . $this->getParameter('gainPerLikePhrasePoints') . " points)."
                );

                $em->flush();

                $event = new GenericEvent(AmbigussEvents::POINTS_GAGNES, array(
                    'membre' => $phrase->getAuteur(),
                ));
                $ed->dispatch(AmbigussEvents::POINTS_GAGNES, $event);
            }

            $action = 'like';
        }
        else if($jaime->getActive() === false) {
            $jaime->setActive(true);
            $action = 'relike';
        }
        else {
            $jaime->setActive(false);
            $action = 'unlike';
        }

        $em->persist($jaime);
        $em->flush();

        if (($action == 'like' || $action == 'relike') && $this->getUser()->getId() != $phrase->getAuteur()->getId()) {
            $event = new GenericEvent(AmbigussEvents::PHRASE_AIMEE, array(
                'membre' => $phrase->getAuteur(),
                'phrase' => $phrase
            ));
            $ed->dispatch(AmbigussEvents::PHRASE_AIMEE, $event);
        }

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
