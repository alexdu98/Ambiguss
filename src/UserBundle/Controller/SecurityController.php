<?php

namespace UserBundle\Controller;

use AmbigussBundle\AmbigussBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class SecurityController extends Controller
{
	public function connexionAction(Request $request)
	{
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('ambiguss_accueil');
		}

		$authenticationUtils = $this->get('security.authentication_utils');

		return $this->render('UserBundle:Security:connexion.html.twig', array(
			'last_username' => $authenticationUtils->getLastUsername(),
			'error'         => $authenticationUtils->getLastAuthenticationError(),
		));
	}

	public function inscriptionAction(Request $request)
	{
        /**
         * @Route("/inscription")
         */

        //créer l'objet membre
        $membre = new \AmbigussBundle\Entity\Membre();

        //ajout des attributs qu'on veut afficher dans le formulaire
        $form = $this->get('form.factory')->createBuilder(FormType::class, $membre)
            ->add('Pseudo', TextType::class)->add('mdp', PasswordType::class)
            ->add('Email', EmailType::class)
            ->add('Sexe', ChoiceType::class, array('choices' => array(
                'Feminin' => null,
                'Masculin' => null
            )))
            ->add('DateNaissance', BirthdayType::class)
            ->add('Newsletter', CheckboxType::class)
            ->add('Valider', SubmitType::class)
            ->getform();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            try {
                if ($form->isValid()) {
                    //affecte le nouveau memebre à un groupe
                    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Groupe');
                    $grp = $repository->find(2); // 2 <=> membre
                    $membre->setGroupe($grp);

                    //affecte un niveau au nouveau membre
                    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Niveau');
                    $grp = $repository->find(1); // 1 <=>niveau
                    $membre->setNiveau($grp);
                    // On enregistre notre objet $advert dans la base de données, par exemple
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($membre);
                    // envoyer email de validation
                    $em->flush();

                    $request->getSession()->getFlashBag()->add('notice', 'Inscription enegistrée, veuillez cliquez sur le lien de confirmation que nous venons de vous envyer.');
                    // rediriger vers l'accueil
                    return $this->redirectToRoute('ambiguss_accueil');
                }
            } catch (UniqueConstraintViolationException $e) {/* erreur*/
            } catch (IntegrityConstraintViolation $e) {/* erreur*/
            }
        }
//sinon
        // On redirige vers le formulaire
        return $this->render('UserBundle:Security:inscription.html.twig', array(
            'form' => $form->createView()
        ));
    }

}