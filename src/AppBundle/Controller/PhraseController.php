<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Glose;
use AppBundle\Entity\MotAmbigu;
use AppBundle\Entity\MotAmbiguPhrase;
use AppBundle\Entity\Partie;
use AppBundle\Entity\Phrase;
use AppBundle\Entity\Reponse;
use AppBundle\Form\Glose\GloseAddType;
use AppBundle\Form\Phrase\PhraseAddType;
use AppBundle\Form\Phrase\PhraseEditType;
use AppBundle\Service\PhraseService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Historique;
use Symfony\Component\Config\Definition\Exception\Exception;

class PhraseController extends Controller
{
	public function newAction(Request $request)
	{
        $secu = $this->get('security.authorization_checker');
        $bag = $this->get('session')->getFlashBag();
        $newPhrase = null;

		if(!$secu->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $msg = "Vous devez être connecté.";
            $bag->add('erreur', $msg);

            return $this->redirectToRoute('fos_user_security_login');
        }

        $phrase = new Phrase();
        $form = $this->createForm(PhraseAddType::class, $phrase);

        $addGloseForm = $this->createForm(GloseAddType::class, new Glose(), array(
            'action' => $this->generateUrl('api_glose_new'),
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $phraseService = $this->get('AppBundle\Service\PhraseService');
            $mapsRep = $request->request->get('phrase')['motsAmbigusPhrase'];

            $res = $phraseService->new($phrase, $this->getUser(), $mapsRep);
            $succes = $res['succes'];

            if($succes) {
                $newPhrase = $phrase;

                // Réinitialise le formulaire
                $form = $this->createForm(PhraseAddType::class, new Phrase());
            }
            else {
                $msg = "Erreur lors de l'insertion de la phrase -> " . $res['message'];
                $bag->add('erreur', $msg);
            }
        }

        return $this->render('AppBundle:Phrase:add.html.twig', array(
            'form' => $form->createView(),
            'newPhrase' => $newPhrase,
            'addGloseForm' => $addGloseForm->createView(),
        ));
	}

	public function editAction(Request $request, Phrase $phrase)
	{
        $secu = $this->get('security.authorization_checker');
        $bag = $this->get('session')->getFlashBag();

		$dateMax = $phrase->getDateCreation()->getTimestamp() + $this->getParameter('dureeAvantJouabiliteSecondes');
		$dateActu = new \DateTime();
        $dateActu = $dateActu->getTimestamp();
        
        if($secu->isGranted('ROLE_MODERATEUR') && !$secu->isGranted('IS_AUTHENTICATED_FULLY')) {

            $msg = "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.";
            $bag->add('danger', $msg);

            return $this->redirectToRoute('fos_user_security_login');
        }
        elseif(!$secu->isGranted('ROLE_MODERATEUR') && $secu->isGranted('IS_AUTHENTICATED_REMEMBERED') && !($phrase->getAuteur() == $this->getUser() && $dateMax < $dateActu)) {

            $msg = "Vous devez être connecté.";
            $bag->add('danger', $msg);

            return $this->redirectToRoute('fos_user_security_login');
        }

        if($dateActu < $dateMax && !$secu->isGranted('ROLE_MODERATEUR')) {

            $form = $this->createForm(PhraseAddType::class, new Phrase(), array(
                'action' => $this->generateUrl('phrase_new')
            ));

            $addGloseForm = $this->createForm(GloseAddType::class, new Glose(), array(
                'action' => $this->generateUrl('api_glose_new'),
            ));

            $form->handleRequest($request);
            
            return $this->render('AppBundle:Phrase:edit.html.twig', array(
                'form' => $form->createView(),
                'phrase' => $phrase,
                'addGloseForm' => $addGloseForm->createView(),
            ));
        }
        else if($secu->isGranted('ROLE_MODERATEUR')) {

            $em = $this->getDoctrine()->getManager();
            $repoJ = $em->getRepository('AppBundle:Jugement');
            $repoTO = $em->getRepository('AppBundle:TypeObjet');
            $repoRep = $em->getRepository('AppBundle:Reponse');

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
            $jugements = $repoJ->findBy(array(
                'typeObjet' => $typeObj,
                'verdict' => null,
                'objetId' => $phrase->getId(),
            ));

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $phraseService = $this->get('AppBundle\Service\PhraseService');

                $mapsRep = $request->request->get('phrase')['motsAmbigusPhrase'];
                $data = $form->getData();

                $phrase->setContenu($data->getContenu());
                $phrase->setSignale($data->getSignale());
                $phrase->setVisible($data->getVisible());
                $phrase->setDateModification(new \DateTime());
                $phrase->setModificateur($this->getUser());
                
                $phrase->normalize();
                $res = $phrase->isValid();

                $succes = $res['succes'];
                $motsAmbigus = $res['motsAmbigus'];

                if($succes) {

                    $phraseService->update($phrase, $this->getUser(), $motsAmbigus, $mapsRep);

                    $newPhrase = $phrase;

                    // Réinitialise le formulaire
                    $phrase = new Phrase();
                    $form = $this->createForm(PhraseEditType::class, $phrase);
                }
                else {
                    $msg = "Erreur lors de l'insertion de la phrase -> " . $res['message'];
                    $bag->add('erreur', $msg);
                }
            }

            // Récupération des réponses du créateur
            $reponses = $repoRep->findBy(array(
                'auteur' => $phrase->getAuteur(),
                'phrase' => $phrase
            ));

            // Extraction de la glose pour un mot ambigu dans une phrase
            $repOri = array();
            foreach ($reponses as $rep) {
                $arr['map_ordre'] = $rep->getMotAmbiguPhrase()->getOrdre();
                $arr['glose_id'] = $rep->getGlose()->getId();
                $repOri[] = $arr;
            }

            return $this->render('AppBundle:Phrase:editModerateur.html.twig', array(
                'form' => $form->createView(),
                'phrase' => $phrase,
                'phraseOri' => $phraseOri,
                'reponsesOri' => $repOri,
                'newPhrase' => $newPhrase,
                'jugements' => $jugements,
                'addGloseForm' => $addGloseForm->createView(),
            ));
        }
        
        throw $this->createAccessDeniedException();
    }
	
}
