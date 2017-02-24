<?php

namespace UserBundle\Controller;

use AmbigussBundle\AmbigussBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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

		//créer l'objet membre
		$membre = new \UserBundle\Entity\Membre();

		//ajout des attributs qu'on veut afficher dans le formulaire
		$form = $this->get('form.factory')->createBuilder(FormType::class, $membre)
			->add('Pseudo', TextType::class, array(
				'attr' => array('placeholder' => 'Pseudo'),
				'invalid_message' => 'Pseudo invalide'
			))
			->add('Connexion', SubmitType::class, array(
				'attr' => array('class' => 'btn btn-primary'),
			))
			->getform();

		return $this->render('UserBundle:Security:connexion.html.twig', array(
			'last_username' => $authenticationUtils->getLastUsername(),
			'error'         => $authenticationUtils->getLastAuthenticationError(),
		));
	}

	public function inscriptionAction(Request $request)
	{
        //créer l'objet membre
        $membre = new \UserBundle\Entity\Membre();

        //ajout des attributs qu'on veut afficher dans le formulaire
        $form = $this->get('form.factory')->createBuilder(FormType::class, $membre)
            ->add('Pseudo', TextType::class, array(
	            'attr' => array('placeholder' => 'Pseudo'),
	            'invalid_message' => 'Pseudo invalide'
            ))
	        ->add('Mdp', RepeatedType::class, array(
		        'type' => PasswordType::class,
		        'options' => array('attr' => array('class' => 'password-field')),
		        'first_options'  => array(
			        'label' => 'Mot de passe',
			        'attr' => array('placeholder' => 'Mot de passe')
		        ),
		        'second_options' => array(
			        'label' => 'Confirmation du mot de passe',
			        'attr' => array('placeholder' => 'Confirmation du mot de passe')
		        ),
		        'invalid_message' => 'Les mots de passe ne sont pas identiques.'
	        ))
            ->add('Email', EmailType::class, array(
	            'attr' => array('placeholder' => 'Email')
            ))
            ->add('Newsletter', CheckboxType::class, array(
	            'label' => "J'accepte de recevoir les newsletter du site",
	            'required' => false
            ))
	        ->add('Conditions', CheckboxType::class, array(
		        'label' => "J'accepte les CGU du site",
		        'required' => true,
		        'mapped' => false
	        ))
            ->add('Valider', SubmitType::class, array(
	            'attr' => array('class' => 'btn btn-primary'),
            ))
            ->getform();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
	        $recaptcha = $this->get('app.recaptcha');
	        if($recaptcha->check($request->request->get('g-recaptcha-response'))){
		        $form->handleRequest($request);
		        if($form->isValid()){
			        // Hash le Mdp
			        $encoder = $this->get('security.password_encoder');
			        $hash = $encoder->encodePassword($membre, $membre->getMdp());

			        $membre->setMdp($hash);

			        // Affecte le nouveau membre à un groupe
			        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Groupe');
			        $grp = $repository->findOneByNom('Membre');
			        $membre->setGroupe($grp);

			        // Affecte un niveau au nouveau membre
			        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
			        $grp = $repository->findOneByTitre('Facile');
			        $membre->setNiveau($grp);

			        // Génère la clé pour la confirmation d'email et l'enregistre dans le champ cleOubliMdp
			        $cleConfirmation = $membre->generateCle();

			        try{
				        // On enregistre le membre dans la base de données
				        $em = $this->getDoctrine()->getManager();
				        $em->persist($membre);
				        $em->flush();
			        }
			        catch(Exception $e){
				        $this->get('session')->setFlash('erreur', "Erreur lors de l'insertion du membre");
			        }

			        // Envoi de l'email confimation/validation
			        $message = \Swift_Message::newInstance()
				        ->setSubject("[Ambiguss] Confirmation d'inscription")
				        ->setFrom(array(
					        "no-reply@ambiguss.calyxe.fr" => "Ambiguss"
				        ))
				        ->setTo(array(
					        $membre->getEmail() => $membre->getPseudo()
				        ))
				        ->setBody($this->renderView('emails/inscription.html.twig', array(
						        'titre'           => "Confirmation d'inscription",
						        'pseudo'          => $membre->getPseudo(),
						        'cleConfirmation' => $cleConfirmation
		                )),
			        'text/html');

			        if($this->get('mailer')->send($message)){
				        $this->get('session')->getFlashBag()->add('succes', 'Inscription réussie, veuillez cliquer sur le lien de confirmation envoyer par email.');
			        }
			        else{
				        $this->get('session')->getFlashBag()->add('erreur', "Inscription réussie, mais l'envoi de
		                l'email de confirmation a échoué. Contactez un administrateur.");
			        }

			        // rediriger vers la page de connexion
			        return $this->redirectToRoute('connexion');
		        }
	        }
	        $this->get('session')->getFlashBag()->add('erreur', "Captcha invalide.");
        }
		// Pas de formulaire envoyé ou erreur
        return $this->render('UserBundle:Security:inscription.html.twig', array(
            'form' => $form->createView()
        ));
    }

	public function inscriptionConfirmationAction(Request $request)
	{
		return $this->render('UserBundle:Security:inscription_confirmation.html.twig');
	}
}