<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\Phrase;
use AppBundle\Event\AmbigussEvents;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Form\Phrase\PhraseAddType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

class PhraseController extends Controller
{
	public function newAction(Request $request, LoggerInterface $logger)
	{
        $secu = $this->get('security.authorization_checker');
        $bag = $this->get('session')->getFlashBag();

		if(!$secu->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $msg = "Vous devez être connecté.";
            $bag->add('danger', $msg);

            return $this->redirectToRoute('fos_user_security_login');
        }

        $newPhrase = null;
        $phrase = new Phrase();
        $form = $this->createForm(PhraseAddType::class, $phrase);

        $addGloseForm = $this->createForm(GloseAddType::class, new Glose(), array(
            'action' => $this->generateUrl('api_glose_new'),
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $phraseService = $this->get('AppBundle\Service\PhraseService');
            $mapRep = $request->request->get('phrase')['motsAmbigusPhrase'] ?? array();

            $res = $phraseService->new($phrase, $this->getUser(), $mapRep);
            $succes = $res['succes'];

            if($succes) {
                $newPhrase = $phrase;

                // Réinitialise le formulaire
                $form = $this->createForm(PhraseAddType::class, new Phrase());

                $ed = $this->get('event_dispatcher');
                $event = new GenericEvent(AmbigussEvents::PHRASE_CREEE, array(
                    'membre' => $this->getUser(),
                    'phrase' => $newPhrase
                ));
                $ed->dispatch(AmbigussEvents::PHRASE_CREEE, $event);
            }
            else {
                $msg = "Erreur lors de l'insertion de la phrase -> " . $res['message'];
                $bag->add('danger', $msg);

                $logInfos = array(
                    'msg' => $msg,
                    'user' => $this->getUser()->getUsername(),
                    'ip' => $request->server->get('REMOTE_ADDR'),
                    'phrase' => $phrase->getContenu()
                );
                $logger->error(json_encode($logInfos));
            }
        }

        return $this->render('AppBundle:Phrase:add.html.twig', array(
            'form' => $form->createView(),
            'newPhrase' => $newPhrase,
            'addGloseForm' => $addGloseForm->createView(),
        ));
	}

	public function editAction(Request $request, LoggerInterface $logger, Phrase $phrase)
	{
        $secu = $this->get('security.authorization_checker');
        $bag = $this->get('session')->getFlashBag();

        if(!$secu->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $msg = "Vous devez être connecté.";
            $bag->add('danger', $msg);

            return $this->redirectToRoute('fos_user_security_login');
        }

        if($phrase->getAuteur() != $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $dureeAvantJouabiliteS = $this->getParameter('dureeAvantJouabiliteSecondes');
		$dateMax = $phrase->getDateCreation()->getTimestamp() + $dureeAvantJouabiliteS;
		$dateActu = new \DateTime();
        $dateActu = $dateActu->getTimestamp();
        $timeExceeded = $dateActu >= $dateMax;

        if($timeExceeded) {
            $bag->add('danger', 'Vous ne pouvez plus modifier votre phrase car cela fait plus de ' . $dureeAvantJouabiliteS / 60 . ' minutes qu\'elle a été créée.');
        }

        $em = $this->getDoctrine()->getManager();
        $repoRep = $em->getRepository('AppBundle:Reponse');

        $form = $this->createForm(PhraseAddType::class, new Phrase(), array(
            'action' => $this->generateUrl('phrase_edit', array('id' => $phrase->getId()))
        ));

        $addGloseForm = $this->createForm(GloseAddType::class, new Glose(), array(
            'action' => $this->generateUrl('api_glose_new'),
        ));

        $phraseOri = clone $phrase;

        // Récupération des réponses du créateur
        $reponses = $repoRep->findBy(array(
            'auteur' => $phrase->getModificateur() ?? $phrase->getAuteur(),
            'phrase' => $phrase
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && !$timeExceeded) {

            $phraseService = $this->get('AppBundle\Service\PhraseService');
            $mapRep = $request->request->get('phrase')['motsAmbigusPhrase'] ?? array();

            $res = $phraseService->update($phrase, $this->getUser(), $form->getData(), $mapRep);
            $succes = $res['succes'];

            if($succes) {
                $newPhrase = $phrase;
                $phraseOri = clone $phrase;

                // Réinitialise le formulaire
                $form = $this->createForm(PhraseAddType::class, new Phrase());

                $ed = $this->get('event_dispatcher');
                $event = new GenericEvent(AmbigussEvents::PHRASE_MODIFIEE, array(
                    'membre' => $this->getUser(),
                    'phrase' => $newPhrase
                ));
                $ed->dispatch(AmbigussEvents::PHRASE_MODIFIEE, $event);
            }
            else {
                $msg = "Erreur lors de la modification de la phrase -> " . $res['message'];
                $bag->add('danger', $msg);

                $logInfos = array(
                    'msg' => $msg,
                    'user' => $this->getUser()->getUsername(),
                    'ip' => $request->server->get('REMOTE_ADDR'),
                    'phrase' => $phrase->getContenu()
                );
                $logger->error(json_encode($logInfos));
            }
        }

        // Extraction de la glose pour un mot ambigu dans une phrase
        $repOri = array();
        foreach ($reponses as $rep) {
            $arr['map_ordre'] = $rep->getMotAmbiguPhrase()->getOrdre();
            $arr['glose_id'] = $rep->getGlose()->getId();
            $repOri[] = $arr;
        }

        return $this->render('AppBundle:Phrase:edit.html.twig', array(
            'form' => $form->createView(),
            'phrase' => $phrase,
            'phraseOri' => $phraseOri,
            'reponsesOri' => $repOri,
            'newPhrase' => $newPhrase,
            'addGloseForm' => $addGloseForm->createView(),
            'timeExceeded' => $timeExceeded
        ));
    }
	
}
