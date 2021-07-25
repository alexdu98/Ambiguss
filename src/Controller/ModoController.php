<?php

namespace App\Controller;

use App\Entity\Glose;
use App\Entity\Historique;
use App\Entity\Signalement;
use App\Entity\Membre;
use App\Entity\Phrase;
use App\Event\GameEvents;
use App\Form\Glose\GloseAddType;
use App\Form\Glose\GloseEditType;
use App\Form\Membre\MembreEditType;
use App\Form\Phrase\PhraseEditType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class ModoController extends AbstractController
{

    public function showGloses()
    {
        $repoG = $this->getDoctrine()->getManager()->getRepository('App:Glose');
        $gloses = $repoG->findAll();

        $glose = new Glose();
        $editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
            'action' => $this->generateUrl('modo_glose_edit', array('id' => 0)),
        ));

        return $this->render('App:Moderation:Glose/list.html.twig', array(
            'gloses' => $gloses,
            'editGloseForm' => $editGloseForm->createView(),
        ));
    }

    public function editGlose(Request $request, Glose $glose)
    {
        $gloseO = clone $glose;
        $form = $this->createForm(GloseEditType::class, $glose);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $historiqueService = $this->container->get('App\Service\HistoriqueService');

            $repoG = $em->getRepository('App:Glose');
            $repoR = $em->getRepository('App:Reponse');

            // Récupère la glose avec la valeur que l'on veut insert
            $gloseM = $repoG->findOneBy(array(
                'valeur' => $glose->getValeur(),
            ));

            // Si la nouvelle valeur de la glose existe déjà et que ce n'est pas la même glose
            if ($gloseM && $gloseM->getId() != $glose->getId()) {
                // Récupère les réponses de la glose en cours de modification
                $reponses = $repoR->findBy(array(
                    'glose' => $glose->getId(),
                ));

                // Modifie les réponses de la glose en cours de modification par celle qui existe déjà
                foreach ($reponses as $reponse) {
                    $reponse->setGlose($gloseM);
                    $em->persist($reponse);
                }

                // Ajoute les liaisons motAmbigu-Glose de la glose que l'on modifie à celle qui existe déjà
                foreach ($glose->getMotsAmbigus() as $motAmbigu) {
                    // Si la glose qui existe déjà n'est pas déjà liée au mot ambigu
                    if (!$gloseM->getMotsAmbigus()->contains($motAmbigu)) {
                        $motAmbigu->addGlose($gloseM);
                        $em->persist($motAmbigu);
                    }
                }

                // On enregistre dans l'historique du modérateur
                $histo = "Fusion de la glose #{$glose->getId()} ({$glose->getValeur()}) => #{$gloseM->getId()} ({$gloseM->getValeur()}).";
                $historiqueService->save($this->getUser(), $histo, false, true);

                // Supprime la glose
                $em->remove($glose);
            }
            // Si la nouvelle valeur de la glose n'existe pas, on enregistre les modifications
            else {
                $infos = array();
                if($gloseO->getValeur() != $glose->getValeur()){
                    $infos[] = "valeur : {$gloseO->getValeur()} => {$glose->getValeur()}";
                }
                if($gloseO->getSignale() != $glose->getSignale()){
                    $oldSignale = $gloseO->getSignale() == false ? 'non' : 'oui';
                    $newSignale = $glose->getSignale() == false ? 'non' : 'oui';
                    $infos[] = "signalé : {$oldSignale} => {$newSignale}";
                }
                if($gloseO->getVisible() != $glose->getVisible()){
                    $oldVisible = $gloseO->getVisible() == false ? 'non' : 'oui';
                    $newVisible = $glose->getVisible() == false ? 'non' : 'oui';
                    $infos[] = "visible : {$oldVisible} => {$newVisible}";
                }


                // On enregistre dans l'historique du modérateur
                $histo ="Modification de la glose #" . $glose->getId() . ' (' . implode(', ', $infos) . ").";
                $historiqueService->save($this->getUser(), $histo, false, true);

                $em->persist($glose);
            }

            if (!empty($gloseM)) {
                $glose = $gloseM;
            }

            // Mise à jour de la glose fusionnée
            $glose->setModificateur($this->getUser());
            $glose->setDateModification(new \DateTime());
            $em->persist($glose);

            $em->flush();

            $res = array(
                'id' => $glose->getId(),
                'valeur' => $glose->getValeur(),
                'modificateurID' => $glose->getModificateur() != null ? $glose->getModificateur()->getId() : '',
                'modificateur' => $glose->getModificateur() != null ? $glose->getModificateur()->getUsername() : '',
                'dateModification' => $glose->getDateModification() != null ? $glose->getDateModification()->format('d/m/Y H:i') : '',
                'dateModificationTS' => $glose->getDateModification() != null ? $glose->getDateModification()->format('U') + $glose->getDateModification()->format('Z') : '',
                'visible' => $glose->getVisible(),
                'signale' => $glose->getSignale()
            );

            return $this->json(array(
                'succes' => true,
                'glose' => $res,
            ));

        }

        throw $this->createNotFoundException();
    }

    public function showPhrases()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('App:Phrase');
        $phrases = $repo->findAll();

        return $this->render('App:Moderation:Phrase/list.html.twig', array(
            'phrases' => $phrases,
        ));
    }

    public function showMembres()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('App:Membre');
        $membres = $repo->findAll();

        $membre = new Membre();
        $editMembreForm = $this->createForm(MembreEditType::class, $membre, array(
            'action' => $this->generateUrl('modo_membre_edit', array('id' => 0)),
        ));

        return $this->render('App:Moderation:Membre/list.html.twig', array(
            'membres' => $membres,
            'editMembreForm' => $editMembreForm->createView(),
        ));
    }

    public function editMembre(Request $request, Membre $membre)
    {
        $membreO = clone $membre;
        $form = $this->createForm(MembreEditType::class, $membre);

        $form->handleRequest($request);

        $succes = false;
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $historiqueService = $this->container->get('App\Service\HistoriqueService');

            $infos = array();
            if($membreO->getSignale() != $membre->getSignale()){
                $oldSignale = $membreO->getSignale() == false ? 'non' : 'oui';
                $newSignale = $membre->getSignale() == false ? 'non' : 'oui';
                $infos[] = "signalé : {$oldSignale} => {$newSignale}";
            }
            if($membreO->getRenamable() != $membre->getRenamable()){
                $oldRenomable = $membreO->getRenamable() == false ? 'non' : 'oui';
                $newRenomable = $membre->getRenamable() == false ? 'non' : 'oui';
                $infos[] = "renomable : {$oldRenomable} => {$newRenomable}";
            }
            if($membreO->getBanni() != $membre->getBanni()){
                $oldBanni = $membreO->getBanni() == false ? 'non' : 'oui';
                $newBanni = $membre->getBanni() == false ? 'non' : 'oui';
                $infos[] = "banni : {$oldBanni} => {$newBanni}";
            }
            if($membreO->getDateDeban() != $membre->getDateDeban()){
                $oldDateDeban = $membreO->getDateDeban() ? $membreO->getDateDeban()->format('d/m/Y H:i') : '';
                $newDateDeban = $membre->getDateDeban() ? $membre->getDateDeban()->format('d/m/Y H:i') : '';
                $infos[] = "date deban : {$oldDateDeban} => {$newDateDeban}";
            }
            if($membreO->getCommentaireBan() != $membre->getCommentaireBan()){
                $infos[] = "commentaire ban : {$membreO->getCommentaireBan()} => {$membre->getCommentaireBan()}";
            }

            // On enregistre dans l'historique du modérateur
            $histo ="Modification du membre #" . $membre->getId() . ' (' . implode(', ', $infos) . ").";
            $historiqueService->save($this->getUser(), $histo, false, true);

            $em->persist($membre);
            $em->flush();

            $succes = true;
        }

        $res = array(
            'id' => $membre->getId(),
            'banni' => $membre->getBanni(),
            'comBan' => $membre->getCommentaireBan(),
            'dateDeban' => $membre->getDateDeban() != null ? $membre->getDateDeban()->format('d/m/Y H:i') : '',
            'dateDebanTS' => $membre->getDateDeban() != null ? $membre->getDateDeban()->format('U') + $membre->getDateDeban()->format('Z') : '',
            'renomable' => $membre->getRenamable(),
            'signale' => $membre->getSignale()
        );

        return $this->json(array(
            'succes' => $succes,
            'membre' => $res
        ));
    }

    public function showMembreSignalements($id)
    {
        $repoJ = $this->getDoctrine()->getManager()->getRepository('App:Signalement');
        $repoTO = $this->getDoctrine()->getManager()->getRepository('App:TypeObjet');

        $typeObj = $repoTO->findOneBy(array('nom' => 'Membre'));
        $signalements = $repoJ->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'objetId' => $id,
        ));

        return $this->json(array(
            'succes' => true,
            'signalements' => $signalements,
        ));
    }

    public function showMotsAmbigusGloses()
    {
        return $this->render('App:Moderation:MAG/searchAndRemove.html.twig');
    }

    public function deleteMotsAmbigusGloses(Request $request)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('delete_mag', $data['token'])) {
            $succes = false;
            $message = null;

            if (!empty($data['motAmbigu']) && !empty($data['glose'])) {
                $em = $this->getDoctrine()->getManager();
                $historiqueService = $this->container->get('App\Service\HistoriqueService');

                $repMA = $em->getRepository('App:MotAmbigu');
                $repG = $em->getRepository('App:Glose');

                $ma = $repMA->find($data['motAmbigu']);
                $g = $repG->find($data['glose']);

                $ma->removeGlose($g);

                // On enregistre dans l'historique du modérateur
                $histo ="Suppression de la liaison entre le mot ambigu #{$ma->getId()} ({$ma->getValeur()}) et la glose #{$g->getId()} ({$g->getValeur()}).";
                $historiqueService->save($this->getUser(), $histo, false, true);

                $em->persist($ma);
                $em->flush();

                $succes = true;
            } else {
                $message = "Tous les champs ne sont pas remplis";
            }

            return $this->json(array(
                'succes' => $succes,
                'message' => $message,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function showGloseSignalements($id)
    {
        $repoS = $this->getDoctrine()->getManager()->getRepository('App:Signalement');
        $repoTO = $this->getDoctrine()->getManager()->getRepository('App:TypeObjet');

        $typeObj = $repoTO->findOneBy(array('nom' => 'Glose'));
        $signalements = $repoS->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'objetId' => $id,
        ));

        return $this->json(array(
            'succes' => true,
            'signalements' => $signalements,
        ));
    }

    public function editSignalement(Request $request, Signalement $signalement)
    {
        $data = $request->request->all();

        if($this->isCsrfTokenValid('signalement_vote', $data['token']))
        {
            $succes = false;

            if (!$signalement->getVerdict()) {
                $em = $this->getDoctrine()->getManager();
                $historiqueService = $this->container->get('App\Service\HistoriqueService');

                $repoTV = $em->getRepository('App:TypeVote');
                $verdict = $repoTV->findOneBy(array('nom' => $data['verdict']));

                // On met à jour le signalement
                $signalement->setDateDeliberation(new \DateTime());
                $signalement->setJuge($this->getUser());
                $signalement->setVerdict($verdict);

                // On enregistre dans l'historique du modérateur
                $histo = "Signalement #{$signalement->getId()}, verdict : {$signalement->getVerdict()->getNom()}.";
                $historiqueService->save($this->getUser(), $histo, false, true);

                // Si le signalement obtient un verdict valide on donne des points
                $msgHisto = '';
                if ($verdict->getNom() == 'Valide') {
                    $gainSignalementValide = $this->getParameter('gainSignalementValide');

                    // Mise à jour du nombre de crédits et de points de l'auteur
                    $signalement->getAuteur()->updateCredits($gainSignalementValide);
                    $signalement->getAuteur()->updatePoints($gainSignalementValide);

                    $msgHisto = ' (+' . $gainSignalementValide . ' crédits/points)';
                }

                // On enregistre dans l'historique du joueur ayant fait le signalement
                $historiqueService->save($signalement->getAuteur(), "Signalement #{$signalement->getId()}, verdict : {$signalement->getVerdict()->getNom()}{$msgHisto}.");

                $em->persist($signalement);
                $em->flush();

                if ($verdict->getNom() == 'Valide') {
                    $ed = $this->get('event_dispatcher');

                    $event = new GenericEvent(GameEvents::SIGNALEMENT_VALIDE, array(
                        'membre' => $signalement->getAuteur(),
                        'signalement' => $signalement
                    ));
                    $ed->dispatch(GameEvents::SIGNALEMENT_VALIDE, $event);

                    $event = new GenericEvent(GameEvents::POINTS_GAGNES, array(
                        'membre' => $signalement->getAuteur(),
                    ));
                    $ed->dispatch(GameEvents::POINTS_GAGNES, $event);
                }

                $succes = true;

                $res = array(
                    'id' => $signalement->getId(),
                    'dateDeliberation' => $signalement->getDateDeliberation() != null ? $signalement->getDateDeliberation()->format('d/m/Y H:i') : '',
                    'dateDeliberationTS' => $signalement->getDateDeliberation() != null ? $signalement->getDateDeliberation()->format('U') + $signalement->getDateDeliberation()->format('Z') : '',
                    'juge' => $signalement->getJuge()->getUsername(),
                    'jugeID' => $signalement->getJuge()->getId(),
                    'verdict' => $signalement->getVerdict()->getNom()
                );
            }

            return $this->json(array(
                'succes' => $succes,
                'signalement' => $res
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function showSignalements()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('App:Signalement');
        $signalements = $repo->getAllWithObject();

        return $this->render('App:Moderation/Signalement:list.html.twig', array(
            'signalements' => $signalements
        ));
    }

    public function editPhrase(Request $request, LoggerInterface $logger, Phrase $phrase)
    {
        $em = $this->getDoctrine()->getManager();
        $repoS = $em->getRepository('App:Signalement');
        $repoTO = $em->getRepository('App:TypeObjet');
        $repoRep = $em->getRepository('App:Reponse');

        $form = $this->createForm(PhraseEditType::class, new Phrase(), array(
            'signale' => $phrase->getSignale(),
            'visible' => $phrase->getVisible(),
        ));

        $addGloseForm = $this->get('form.factory')->create(GloseAddType::class, new Glose(), array(
            'action' => $this->generateUrl('api_glose_new'),
        ));

        $newPhrase = null;
        $phraseOri = clone $phrase;
        $typeObj = $repoTO->findOneBy(array('nom' => 'Phrase'));
        $signalements = $repoS->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'objetId' => $phrase->getId(),
        ));

        // Récupération des réponses du créateur
        $reponses = $repoRep->findBy(array(
            'auteur' => $phrase->getModificateur() ?? $phrase->getAuteur(),
            'phrase' => $phrase
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $phraseService = $this->get('App\Service\PhraseService');
            $mapRep = $request->request->get('phrase')['motsAmbigusPhrase'] ?? array();

            $res = $phraseService->updateModo($phrase, $this->getUser(), $form->getData(), $mapRep);
            $succes = $res['succes'];

            if($succes) {
                $newPhrase = $phrase;

                $phraseOri = clone $phrase;
            }
            else {
                $bag = $this->get('session')->getFlashBag();

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

        return $this->render('App:Moderation/Phrase:edit.html.twig', array(
            'form' => $form->createView(),
            'phrase' => $phrase,
            'phraseOri' => $phraseOri,
            'reponsesOri' => $repOri,
            'newPhrase' => $newPhrase,
            'signalements' => $signalements,
            'addGloseForm' => $addGloseForm->createView(),
        ));
    }

}
