<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Historique;
use UserBundle\Form\ModifProfilType;

class UtilisateurController extends Controller
{

	public function profilAction(Request $request, \UserBundle\Entity\Membre $user = null)
	{
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('user_connexion');
		}

		$repoPh = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
		$nbPhrases = $repoPh->countAllVisible();

		$repoP = $this->getDoctrine()->getManager()->getRepository('AppBundle:Partie');

		if($user == null)
		{
			$nbParties = $repoP->countAllGamesByMembre($this->getUser());
			return $this->render('UserBundle:Utilisateur:myprofil.html.twig', array(
				'user' => $this->getUser(),
				'nbParties' => $nbParties['nbParties'],
				'nbPhrases' => $nbPhrases['nbPhrases'],
			));
		}
		else
		{
			$nbParties = $repoP->countAllGamesByMembre($user);
			return $this->render('UserBundle:Utilisateur:otherprofil.html.twig', array(
				'user' => $user,
				'nbParties' => $nbParties['nbParties'],
				'nbPhrases' => $nbPhrases['nbPhrases'],
			));
		}
	}

    public function ModifProfilAction(Request $request){

        $membre = new \UserBundle\Entity\Membre();
        $form = $this->get('form.factory')->create(ModifProfilType::class, $membre, array(
	        'email' => $this->getUser()->getEmail(),
	        'newsletter' => $this->getUser()->getNewsletter(),
	        'sexe' => $this->getUser()->getSexe(),
	        'dateNaissance' => $this->getUser()->getDateNaissance(),
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ) {

            $data = $form->getData();

            $repoUser = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
            //$membre = null;

	        $membre = $repoUser->find($this->getUser()->getid());
	        $histJoueur = new Historique();
	        $histJoueur->setMembre($this->getUser());

	        $success = true;

	        if(isset($request->request->get('modif_profil')['ValiderEmail']))
	        {
		        $membre->setEmail($data->getEmail());
		        $histJoueur->setValeur("Modification de l'email (IP : " . $_SERVER['REMOTE_ADDR'] . " / " . $this->getUser()->getEmail() . " => " . $data->getEmail() . ").");
	        }
	        else if(isset($request->request->get('modif_profil')['ValiderMdp']))
            {
	            $mdpActu = $request->request->get('modif_profil')['mdpActu'];

	            if(password_verify($mdpActu, $this->getUser()->getMdp()))
	            {
		            // Hash le Mdp
		            $encoder = $this->get('security.password_encoder');
		            $hash = $encoder->encodePassword($membre, $data->getMdp());

		            $membre->setMdp($hash);
		            $histJoueur->setValeur("Modification du mot de passe (IP : " . $_SERVER['REMOTE_ADDR'] . ").");
	            }
	            else
	            {
		            $success = false;
		            $this->get('session')->getFlashBag()->add('erreur', 'Mot de passe actuel incorrect.');
	            }
            }
	        else if(isset($request->request->get('modif_profil')['ValiderInfos']))
	        {
		        $oldSexe = $this->getUser()->getSexe();
		        $newSexe = $data->getSexe();
		        $membre->setSexe($data->getSexe());

		        $oldDateNaissance = $this->getUser()->getDateNaissance()->format('d/m/Y');
		        $newDateNaissance = $data->getDateNaissance()->format('d/m/Y');
		        $membre->setDateNaissance($data->getDateNaissance());

		        $oldNewsletter = $this->getUser()->getNewsletter() === false ? 'non abonné' : 'abonné';
		        $newNewsletter = $data->getNewsletter() === false ? 'non abonné' : 'abonné';
		        $membre->setNewsletter($data->getNewsletter());

		        $histJoueur->setValeur("Modification des informations (sexe : " . $oldSexe . ", date de naissance : " . $oldDateNaissance . ", newsletter : " . $oldNewsletter . " => sexe : " . $newSexe . ", date de naissance : " . $newDateNaissance . ", newsletter : " . $newNewsletter . ").");
	        }

	        $em = $this->getDoctrine()->getManager();

	        $em->persist($membre);
	        $em->persist($histJoueur);

	        if($success)
	        {
		        try
		        {
			        $em->flush();
			        $this->get('session')->getFlashBag()->add('succes', 'Vos informations ont bien été modifiées');
		        }
		        catch(\Exception $e)
		        {
			        $this->get('session')->getFlashBag()->add('erreur', 'Erreur');
		        }
	        }

	        // Réinitialise le formulaire
	        $membre = new \UserBundle\Entity\Membre();
	        $form = $this->get('form.factory')->create(ModifProfilType::class, $membre, array(
		        'email' => $this->getUser()->getEmail(),
		        'newsletter' => $this->getUser()->getNewsletter(),
		        'sexe' => $this->getUser()->getSexe(),
		        'dateNaissance' => $this->getUser()->getDateNaissance(),
	        ));

        }

	    return $this->render('UserBundle:Utilisateur:modifProfil.html.twig', array(
            'form' => $form->createView()
        ));

    }
}