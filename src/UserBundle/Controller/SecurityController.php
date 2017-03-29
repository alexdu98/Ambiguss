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
use UserBundle\Form\MembreConnexionType;
use UserBundle\Form\MembreOubliPassResetType;
use UserBundle\Form\MembreOubliPassType;
use UserBundle\Form\MembreType;

class SecurityController extends Controller
{
	public function connexionAction(Request $request)
	{
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('app_accueil');
		}

		$authenticationUtils = $this->get('security.authentication_utils');

		//créer l'objet membre
		$membre = new \UserBundle\Entity\Membre();

		//ajout des attributs qu'on veut afficher dans le formulaire
		$form = $this->get('form.factory')->create(MembreConnexionType::class, $membre);

		return $this->render('UserBundle:Security:connexion.html.twig', array(
			'form' => $form->createView(),
			'last_username' => $authenticationUtils->getLastUsername(),
			'erreur'         => $authenticationUtils->getLastAuthenticationError(),
		));
	}

	public function inscriptionAction(Request $request, $provider = null)
	{
        //créer l'objet membre
        $membre = new \UserBundle\Entity\Membre();

        //ajout des attributs qu'on veut afficher dans le formulaire
        $form = $this->get('form.factory')->create(MembreType::class, $membre);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {

        	// FACEBOOK & TWITTER
        	if(!empty($provider))
        	{
        		$data = json_decode($request->get('data'));

        		$membre->setIdFacebook($data->id);
        		$membre->setEmail($data->email);
        		$sexe = $data->gender == "male" ? "Homme" : "Femme";
        		$membre->setSexe($sexe);
        		$membre->setActif(true);

		        // Affecte le nouveau membre à un groupe
		        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Groupe');
		        $grp = $repository->findOneByNom('Membre');
		        $membre->setGroupe($grp);

		        // Affecte un niveau au nouveau membre
		        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
		        $grp = $repository->findOneByTitre('Facile');
		        $membre->setNiveau($grp);

		        $validator = $this->get('validator');
		        $erreurs = $validator->validate($membre);

		        if(count($erreurs) > 0){
		        	$messages = array();
			        foreach($erreurs as $erreur){
						$messages[] = $erreur->getPropertyPath() . " : " . $erreur->getMessage();
		        	}
			        return $this->json(array(
			        	'nb' => count($erreurs),
				        'erreur' => $messages
			        ));
		        }

		        $em = $this->getDoctrine()->getManager();
		        try{
			        $em->persist($membre);
			        $em->flush();
		        }
		        catch(\Exception $e){
			        return $this->json(array(
				        'erreur' => $e
			        ));
		        }

        		return $this->json(array(
			        'data' => $request->get('data')
		        ));
	        }
	        // AMBIGUSS
	        else{
		        $recaptcha = $this->get('app.recaptcha');
		        $recap = $recaptcha->check($request->request->get('g-recaptcha-response'));
		        if($recap->succes){
			        $form->handleRequest($request);
			        if($form->isValid()){
				        $membre = $form->getData();
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

				        // On enregistre le membre dans la base de données
				        $em = $this->getDoctrine()->getManager();
				        try{
					        $em->persist($membre);
					        $em->flush();
				        }
				        catch(\Exception $e){
					        $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion du membre");
				        }

				        // Envoi de l'email confimation/validation
				        $message = \Swift_Message::newInstance()->setSubject("[Ambiguss] Confirmation d'inscription")->setFrom(array(
						        "no-reply@ambiguss.calyxe.fr" => "Ambiguss"
					        ))->setTo(array(
						        $membre->getEmail() => $membre->getPseudo()
					        ))->setBody($this->renderView('emails/inscription.html.twig', array(
						        'titre'           => "Confirmation d'inscription",
						        'pseudo'          => $membre->getPseudo(),
						        'cleConfirmation' => $cleConfirmation
					        )), 'text/html');

				        if($this->get('mailer')->send($message)){
					        $this->get('session')->getFlashBag()->add('succes', 'Inscription réussie, veuillez cliquer sur le lien de confirmation envoyé par email.');
				        }
				        else{
					        $this->get('session')->getFlashBag()->add('erreur', "Inscription réussie, mais l'envoi de
		                l'email de confirmation a échoué. Contactez un administrateur.");
				        }

				        // rediriger vers la page de connexion
				        return $this->redirectToRoute('user_connexion');
			        }
		        }
		        else{
			        $erreurStr = "";
			        foreach($recap->erreurs as $erreur){
				        $erreurStr .= $erreur;
			        }
			        $this->get('session')->getFlashBag()->add('erreur', $erreurStr);
		        }
	        }
        }
		// Pas de formulaire envoyé ou erreur
        return $this->render('UserBundle:Security:inscription.html.twig', array(
            'form' => $form->createView()
        ));
    }

	public function inscriptionConfirmationAction(Request $request, $cle)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
		$membre = $repository->findOneByCleOubliMdp($cle);
		if($membre){
			$membre->setCleOubliMdp(null);
			$membre->setActif(true);

			$em = $this->getDoctrine()->getManager();
			$em->persist($membre);
			$em->flush();

			$this->get('session')->getFlashBag()->add('succes', "Inscription confirmé. Vous pouvez maintenant vous connecter.");

			// rediriger vers la page de connexion
			return $this->redirectToRoute('user_connexion');
		}

		throw $this->createNotFoundException();

	}

	public function oubliMdpAction(Request $request)
	{
		$form = $this->get('form.factory')->create(MembreOubliPassType::class);

		// Si la requête est en POST
		if ($request->isMethod('POST')) {
			$recaptcha = $this->get('app.recaptcha');
			$recap = $recaptcha->check($request->request->get('g-recaptcha-response'));
			if($recap->succes){
				$form->handleRequest($request);
				$data = $form->getData();
				if($form->isValid()){

					$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
					$membre = $repository->findOneByPseudoOrEmail($data['PseudoOuEmail']);

					// Génère la clé pour la réinitialisation de mot de passe et l'enregistre dans le champ cleOubliMdp
					$cleConfirmation = $membre->generateCle();

					// On enregistre le membre dans la base de données
					$em = $this->getDoctrine()->getManager();
					try{
						$em->persist($membre);
						$em->flush();
					}
					catch(\Exception $e){
						$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de la mise à jour du membre");
					}

					// Envoi de l'email confimation/validation
					$message = \Swift_Message::newInstance()
						->setSubject("[Ambiguss] Réinitialisation de mot de passe")
						->setFrom(array(
							"no-reply@ambiguss.calyxe.fr" => "Ambiguss"
						))
						->setTo(array(
							$membre->getEmail() => $membre->getPseudo()
						))
						->setBody($this->renderView('emails/oubli_mdp.html.twig', array(
							'titre'           => "Réinitialisation de mot de passe",
							'pseudo'          => $membre->getPseudo(),
							'cleConfirmation' => $cleConfirmation
						)),
							'text/html');

					if($this->get('mailer')->send($message)){
						$this->get('session')->getFlashBag()->add('succes', 'Veuillez cliquer sur le lien de réinitialisation de mot de passe envoyer par email.');
					}
					else{
						$this->get('session')->getFlashBag()->add('erreur', "L'envoi de
		                l'email de réinitialisation de mot de passe a échoué. Contactez un administrateur.");
					}

					// rediriger vers la page de connexion
					return $this->render('UserBundle:Security:oubli_mdp.html.twig', array(
						'form' => $form->createView()
					));
				}
			}
			else{
				$erreurStr = "";
				foreach($recap->erreurs as $erreur){
					$erreurStr .= $erreur;
				}
				$this->get('session')->getFlashBag()->add('erreur', $erreurStr);
			}
		}

		return $this->render('UserBundle:Security:oubli_mdp.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function oubliMdpReinitialisationAction(Request $request, $cle)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
		$membre = $repository->findOneByCleOubliMdp($cle);
		if($membre){
			$form = $this->get('form.factory')->create(MembreOubliPassResetType::class);

			// Si la requête est en POST
			if ($request->isMethod('POST')) {
				$form->handleRequest($request);
				$data = $form->getData();
				if($form->isValid()){
					// Hash le Mdp
					$encoder = $this->get('security.password_encoder');
					$hash = $encoder->encodePassword($membre, $data['Mdp']);

					$membre->setMdp($hash);
					$membre->setCleOubliMdp(null);

					// On enregistre le membre dans la base de données
					$em = $this->getDoctrine()->getManager();
					try{
						$em->persist($membre);
						$em->flush();
					}
					catch(\Exception $e){
						$this->get('session')->getFlashBag()->add('erreur', "Erreur lors de la mise à jour du membre");
					}

					$this->get('session')->getFlashBag()->add('succes', 'Votre mot de passe a bien été modifié.');

					// rediriger vers la page de connexion
					return $this->redirectToRoute('user_connexion');
				}
			}

			return $this->render('UserBundle:Security:oubli_mdp_reinitialisation.html.twig', array(
				'form' => $form->createView()
			));
		}

		throw $this->createNotFoundException();

	}
}