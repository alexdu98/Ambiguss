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
        $gloses = $repoG->getSignalees();

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
        $form = $this->createForm(GloseEditType::class, $glose);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

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

                // Supprime la glose
                $em->remove($glose);
            }
            // Si la nouvelle valeur de la glose n'existe pas, on enregistre les modifications
            else {
                $em->persist($glose);
            }

            $em->flush();

            if (!empty($gloseM)) {
                $glose = $gloseM;
            }

            $res = array(
                'id' => $glose->getId(),
                'valeur' => $glose->getValeur(),
                'modificateur' => $glose->getModificateur() != null ? $glose->getModificateur()->getUsername() : '',
                'dateModification' => $glose->getDateModification() != null ? $glose->getDateModification()->format('d/m/Y à H:i') : '',
                'signale' => $glose->getSignale(),
            );

            return $this->json(array(
                'succes' => true,
                'glose' => $res,
            ));

        }

        throw $this->createNotFoundException();
    }

    public function deleteGloseAction(Request $request, Glose $glose)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('delete_glose', $data['token'])) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($glose);
            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function countReponsesAction(Glose $glose)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Reponse');
        $reponses = $repo->findBy(array(
            'glose' => $glose,
        ));

        return $this->json(array(
            'reponses' => count($reponses),
        ));
    }

    public function showPhrasesAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $phrases = $repo->getSignalees();

        return $this->render('AppBundle:Phrase:getAll.html.twig', array(
            'phrases' => $phrases,
        ));
    }

    public function showMembresAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
        $membres = $repo->findBy(array(
            'signale' => true
        ));

        $membre = new Membre();
        $editMembreForm = $this->createForm(MembreEditType::class, $membre, array(
            'action' => $this->generateUrl('modo_membre_edit', array('id' => 0)),
        ));

        return $this->render('AppBundle:Membre:getAll.html.twig', array(
            'membres' => $membres,
            'editMembreForm' => $editMembreForm->createView(),
        ));
    }

    public function unsignaleMembreAction(Request $request, Membre $membre)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('unsignale_membre', $data['token'])) {
            $membre->setSignale(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function banMembreAction(Request $request, Membre $membre)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('ban_membre', $data['token'])) {
            $membre->setBanni(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function editMembreAction(Request $request, Membre $membre)
    {

    }

    public function showMembreJugementsAction($id)
    {
        $repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
        $repoTO = $this->getDoctrine()->getManager()->getRepository('AppBundle:TypeObjet');

        $typeObj = $repoTO->findOneBy(array('nom' => 'Membre'));
        $jugements = $repoJ->findBy(array(
            'typeObjet' => $typeObj,
            'verdict' => null,
            'idObjet' => $id,
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

                $repMA = $em->getRepository('AppBundle:MotAmbigu');
                $repG = $em->getRepository('AppBundle:Glose');

                $ma = $repMA->find($data['motAmbigu']);
                $g = $repG->find($data['glose']);

                $ma->removeGlose($g);

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

    public function unsignalePhraseAction(Request $request, Phrase $phrase)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('unsignale_phrase', $data['token'])) {
            $phrase->setSignale(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($phrase);
            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

    public function deletePhraseAction(Request $request, Phrase $phrase)
    {
        $data = $request->request->all();

        if ($this->isCsrfTokenValid('delete_phrase', $data['token'])) {
            $phrase->setVisible(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($phrase);
            $em->flush();

            return $this->json(array(
                'succes' => true,
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
            'idObjet' => $id,
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

            $repoJ = $em->getRepository('AppBundle:Jugement');
            $repoTV = $em->getRepository('AppBundle:TypeVote');

            // On met à jour le jugement
            $jugement = $repoJ->find($id);
            $jugement->setDateDeliberation(new \DateTime());
            $jugement->setJuge($this->getUser());
            $jugement->setVerdict($repoTV->findOneBy(array('nom' => $data['verdict'])));

            // On enregistre dans l'historique du joueur
            $histJoueur = new Historique();
            $histJoueur->setMembre($jugement->getAuteur());
            $histJoueur->setValeur("Jugement n°" . $jugement->getId() . ", verdict : " . $jugement->getVerdict()->getNom() . ".");

            $em->persist($jugement);
            $em->persist($histJoueur);

            $em->flush();

            return $this->json(array(
                'succes' => true,
            ));
        }

        throw new InvalidCsrfTokenException();
    }

}
