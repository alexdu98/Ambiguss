<?php

namespace AppBundle\Controller;

use AppBundle\Form\Search\SearchGloseType;
use AppBundle\Form\Search\SearchMembreType;
use AppBundle\Form\Search\SearchPhraseType;
use AppBundle\Form\Glose\GloseEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Historique;
use AppBundle\Form\Administration\MembreType;

class AdminController extends Controller
{

    public function mainAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('AppBundle:Administration:accueil.html.twig', array());
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function statistiquesAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $stat = array();

            $repoV = $this->getDoctrine()->getManager()->getRepository('AppBundle:Visite');
            $stat['visites'] = $repoV->getStat();

            $repoM = $this->getDoctrine()->getManager()->getRepository('AppBundle:Membre');
            $stat['membres'] = $repoM->getStat();

            $repoPh = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
            $stat['phrases'] = $repoPh->getStat();

            $repoMA = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
            $stat['motsAmbigus'] = $repoMA->getStat();

            $repoG = $this->getDoctrine()->getManager()->getRepository('AppBundle:Glose');
            $stat['gloses'] = $repoG->getStat();

            $repoPa = $this->getDoctrine()->getManager()->getRepository('AppBundle:Partie');
            $stat['parties'] = $repoPa->getStat();

            $repoR = $this->getDoctrine()->getManager()->getRepository('AppBundle:Reponse');
            $stat['reponses'] = $repoR->getStat();

            $repoJ = $this->getDoctrine()->getManager()->getRepository('AppBundle:Jugement');
            $stat['jugements'] = $repoJ->getStat();

            return $this->render('AppBundle:Administration:statistiques.html.twig', array(
                'stat' => $stat,
            ));
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function membresAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form = $this->get('form.factory')->create(SearchMembreType::class, null, array(
                'action' => $this->generateUrl('administration_membre_edit', array('id' => null)),
            ));

            return $this->render('AppBundle:Administration:membres.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function editMembreAction(Request $request, \AppBundle\Entity\Membre $user)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $oldUser = clone($user);

            $form = $this->get('form.factory')->create(MembreType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!empty($user->getMdp())) {
                    // Hash le Mdp
                    $encoder = $this->get('security.password_encoder');
                    $hash = $encoder->encodePassword($user, $user->getMdp());

                    $user->setMdp($hash);
                } else {
                    $user->setMdp($oldUser->getMdp());
                }

                // On enregistre dans l'historique du joueur
                $histJoueur = new Historique();
                $histJoueur->setValeur("Profil modifié par un administrateur");
                $histJoueur->setMembre($user);

                // On enregistre dans l'historique de l'admin
                $histAdmin = new Historique();
                $histAdmin->setValeur("Profil " . $user->getId() . " modifié");
                $histAdmin->setMembre($this->getUser());

                $em = $this->getDoctrine()->getManager();
                $em->persist($histJoueur);
                $em->persist($histAdmin);

                try {
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('succes', 'Membre mis à jour avec succès');
                } catch (\Exception $e) {
                    $this->get('session')->getFlashBag()->add('erreur', 'Erreur');
                }
            }

            return $this->render('AppBundle:Administration:membre_edit.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function phrasesAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form = $this->get('form.factory')->create(SearchPhraseType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $request->request->get('administrationbundle_phrase');

                $repP = $this->getDoctrine()->getRepository('AppBundle:Phrase');
                $res = null;
                if (!empty($data['idPhrase'])) {
                    $res = $repP->findBy(array('id' => $data['idPhrase']));
                } else if (!empty($data['contenuPhrase'])) {
                    $res = $repP->findLike($data['contenuPhrase']);
                } else if (!empty($data['idAuteur'])) {
                    $res = $repP->findBy(array('auteur' => $data['idAuteur']));
                }

                return $this->json(array(
                    'succes' => true,
                    'phrases' => $res,
                ));
            }

            return $this->render('AppBundle:Administration:phrases.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    public function glosesAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $form = $this->get('form.factory')->create(SearchGloseType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $request->request->get('administrationbundle_glose');

                $repG = $this->getDoctrine()->getRepository('AppBundle:Glose');
                $res = null;
                if (!empty($data['idGlose'])) {
                    $res = $repG->findBy(array('id' => $data['idGlose']));
                } else {
                    if (!empty($data['contenuGlose'])) {
                        $res = $repG->findLike($data['contenuGlose']);
                    } else {
                        if (!empty($data['idAuteur'])) {
                            $res = $repG->findBy(array('auteur' => $data['idAuteur']));
                        }
                    }
                }

                return $this->json(array(
                    'succes' => true,
                    'gloses' => $res,
                ));
            }

            $glose = new \AppBundle\Entity\Glose();
            $editGloseForm = $this->get('form.factory')->create(GloseEditType::class, $glose, array(
                'action' => $this->generateUrl('ambiguss_glose_edit'),
            ));

            return $this->render('AppBundle:Administration:gloses.html.twig', array(
                'form' => $form->createView(),
                'editGloseForm' => $editGloseForm->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('erreur', "L'accès à l'administration nécessite d'être connecté sans le système d'auto-connexion.");

            return $this->redirectToRoute('fos_user_security_login');
        }
    }
}