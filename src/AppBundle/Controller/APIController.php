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
				$jugement->setIdObjet($request->request->get('jugement_add')['idObjet']);
				$jugement->setAuteur($this->getUser());

				$histMsg = null;
                $repo = null;
				if($jugement->getTypeObjet()->getNom() == 'Phrase') {
					$repo = $em->getRepository('AppBundle:Phrase');
                    $histMsg = "Signalement de la phrase n°" . $jugement->getIdObjet() . ".";
				}
				elseif($jugement->getTypeObjet()->getNom() == 'Glose') {
                    $repo = $em->getRepository('AppBundle:Glose');
                    $histMsg = "Signalement de la glose n°" . $jugement->getIdObjet() . ".";
				}

                $obj = $repo->find($jugement->getIdObjet());
                $obj->setSignale(true);

                // On enregistre dans l'historique du joueur
                $histJoueur = new Historique();
                $histJoueur->setMembre($this->getUser());
                $histJoueur->setValeur($histMsg);

				$em->persist($jugement);
				$em->persist($obj);
				$em->persist($histJoueur);

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

    public function newGloseAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
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

                // Si le mot ambigu n'existe pas on le créé (cas création de phrase)
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

                    // Si la liaison MA-G n'existait pas déjà on la créé
                    if (!$isLinked)
                        $motAmbigu->addGlose($glose);
                }

                // Les 2 premières gloses d'un mot ambigu sont gratuites
                $nbGloses = $motAmbigu && $motAmbigu->getGloses()->count() >= 2 ? $motAmbigu->getGloses()->count() : 0;

                // On débite les crédits
                $cout = -($nbGloses * $coutNewGlose);
                $this->getUser()->updateCredits($cout);

                // On enregistre dans l'historique du joueur
                $histJoueur = new Historique();
                $histJoueur->setMembre($this->getUser());
                $histJoueur->setValeur("Liaison de la glose n°" . $glose->getId() . " avec le mot ambigu n°" . $motAmbigu->getId() . ".");

                $em->persist($glose);
                $em->persist($motAmbigu);
                $em->persist($this->getUser());
                $em->persist($histJoueur);

                $em->flush();

                $res = array(
                    'id' => $glose->getId(),
                    'valeur' => $glose->getValeur(),
                );

                return $this->json(array(
                    'succes' => true,
                    'glose' => $res,
                    'liaisonExiste' => $isLinked
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
            'ownerId' => $MA->getId(),
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
            'ownerId' => $G->getId(),
            'links' => $MA
        );

        return $this->json($res);
    }

}
