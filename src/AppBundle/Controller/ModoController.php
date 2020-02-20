<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\Historique;
use AppBundle\Entity\Membre;
use AppBundle\Entity\Phrase;
use AppBundle\Form\Glose\GloseEditType;
use AppBundle\Form\Membre\MembreEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class ModoController extends Controller
{

    public function showGlosesAction()
    {
        $repoG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
        $gloses = $repoG->findAll();

        $glose = new Glose();
        $editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
            'action' => $this->generateUrl('modo_glose_edit', array('id' => 0)),
        ));

        return $this->render('AppBundle:Glose:getAll.html.twig', array(
            'gloses' => $gloses,
            'editGloseForm' => $editGloseForm->createView(),
        ));
    }

    public function editGloseAction(Request $request, Glose $glose)
    {
        $gloseO = clone $glose;
        $form = $this->createForm(GloseEditType::class, $glose);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

            $repoG = $em->getRepository('AppBundle:Glose');
            $repoR = $em->getRepository('AppBundle:Reponse');

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
                $histo = '[modo] Fusion de la glose #' . $glose->getId() . '(' . $glose->getValeur() . ') => #' . $gloseM->getId() . ' (' . $gloseM->getValeur() . ').';
                $historiqueService->save($this->getUser(), $histo);

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

                $histo ="[modo:{$request->server->get('REMOTE_ADDR')}] Modification de la glose #" . $glose->getId() . ' (' . implode(', ', $infos) . ").";

                // On enregistre dans l'historique du modérateur
                $historiqueService->save($this->getUser(), $histo);

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

    public function showPhrasesAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $phrases = $repo->findAll();

        return $this->render('AppBundle:Phrase:getAll.html.twig', array(
            'phrases' => $phrases,
        ));
    }

    public function showMembresAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
        $membres = $repo->findAll();

        $membre = new Membre();
        $editMembreForm = $this->createForm(MembreEditType::class, $membre, array(
            'action' => $this->generateUrl('modo_membre_edit', array('id' => 0)),
        ));

        return $this->render('AppBundle:Membre:getAll.html.twig', array(
            'membres' => $membres,
            'editMembreForm' => $editMembreForm->createView(),
        ));
    }

    public function editMembreAction(Request $request, Membre $membre)
    {
        $membreO = clone $membre;
        $form = $this->createForm(MembreEditType::class, $membre);

        $form->handleRequest($request);

        $succes = false;
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

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

            $histo ="[modo:{$request->server->get('REMOTE_ADDR')}] Modification du membre #" . $membre->getId() . ' (' . implode(', ', $infos) . ").";

            // On enregistre dans l'historique du modérateur
            $historiqueService->save($this->getUser(), $histo);

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

    public function showMembreJugementsAction($id)
    {
        $repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
        $repoTO = $this->getDoctrine()->getManager()->getRepository('AppBundle:TypeObjet');
        $repoM = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');

        $typeObj = $repoTO->findOneBy(array('nom' => 'Membre'));
        $jugements = $repoJ->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'objetId' => $id,
        ));

        return $this->json(array(
            'succes' => true,
            'jugements' => $jugements,
        ));
    }

    public function showMotsAmbigusGlosesAction()
    {
        return $this->render('AppBundle:MAG:get.html.twig');
    }

    public function deleteMotsAmbigusGlosesAction(Request $request)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('delete_mag', $data['token'])) {
            $succes = false;
            $message = null;

            if (!empty($data['motAmbigu']) && !empty($data['glose'])) {
                $em = $this->getDoctrine()->getManager();
                $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

                $repMA = $em->getRepository('AppBundle:MotAmbigu');
                $repG = $em->getRepository('AppBundle:Glose');

                $ma = $repMA->find($data['motAmbigu']);
                $g = $repG->find($data['glose']);

                $ma->removeGlose($g);

                $histo ="[modo:{$request->server->get('REMOTE_ADDR')}] Suppression de la liaison entre le mot ambigu #{$ma->getId()} ({$ma->getValeur()}) et la glose #{$g->getId()} ({$g->getValeur()}).";

                // On enregistre dans l'historique du modérateur
                $historiqueService->save($this->getUser(), $histo);

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

    public function showGloseJugementsAction($id)
    {
        $repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
        $repoTO = $this->getDoctrine()->getManager()->getRepository('AppBundle:TypeObjet');

        $typeObj = $repoTO->findOneBy(array('nom' => 'Glose'));
        $jugements = $repoJ->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'objetId' => $id,
        ));

        return $this->json(array(
            'succes' => true,
            'jugements' => $jugements,
        ));
    }

    public function editJugementAction(Request $request, $id)
    {
        $data = $request->request->all();

        if($this->isCsrfTokenValid('jugement_vote', $data['token']))
        {
            $em = $this->getDoctrine()->getManager();
            $historiqueService = $this->container->get('AppBundle\Service\HistoriqueService');

            $repoJ = $em->getRepository('AppBundle:Jugement');
            $repoTV = $em->getRepository('AppBundle:TypeVote');

            // On met à jour le jugement
            $jugement = $repoJ->find($id);
            $jugement->setDateDeliberation(new \DateTime());
            $jugement->setJuge($this->getUser());
            $jugement->setVerdict($repoTV->findOneBy(array('nom' => $data['verdict'])));

            $histo ="[modo:{$request->server->get('REMOTE_ADDR')}] Jugement #{$jugement->getId()}, verdict : {$jugement->getVerdict()->getNom()}.";

            // On enregistre dans l'historique du modérateur
            $historiqueService->save($this->getUser(), $histo);

            // On enregistre dans l'historique du joueur ayant fait le signalement
            $historiqueService->save($jugement->getAuteur(), "Jugement #{$jugement->getId()}, verdict : {$jugement->getVerdict()->getNom()}.");

            $em->persist($jugement);

            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

}
