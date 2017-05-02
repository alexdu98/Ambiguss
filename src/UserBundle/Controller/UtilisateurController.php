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

		$repoPh = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
		$nbPhrases = $repoPh->countAll();

		$repoP = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Partie');

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
        ));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ) {

            $data = $form->getData();

            $repoUser = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
            //$membre = null;

	        $membre = $repoUser->find($this->getUser()->getid());
	        $histJoueur = new Historique();
	        $histJoueur->setMembre($this->getUser());

	        if(!empty($data->getEmail()))
	        {
		        $histJoueur->setValeur("Modification de l'email (IP : " . $_SERVER['REMOTE_ADDR'] . " / " . $this->getUser()->getEmail() . " => " . $data->getEmail() . ").");
		        $membre->setEmail($data->getEmail());
	        }
	        else if(!empty($data->getMdp()))
            {
                // Hash le Mdp
                $encoder = $this->get('security.password_encoder');
                $hash = $encoder->encodePassword($membre, $data->getMdp());

                $membre->setMdp($hash);
	            $histJoueur->setValeur("Modification du mot de passe (IP : " . $_SERVER['REMOTE_ADDR'] . ").");
            }
	        else if($data->getNewsletter() !== null)
	        {
		        $valactu = $this->getUser()->getNewsletter() === false ? 'non abonné' : 'abonné';
		        $valnew = $data->getNewsletter() === false ? 'non abonné' : 'abonné';
		        $membre->setNewsletter($data->getNewsletter());
		        $histJoueur->setValeur("Modification de l'inscription à la newsletter (" . $valactu . " => " . $valnew . ").");
	        }

	        $em = $this->getDoctrine()->getManager();

	        $em->persist($membre);
	        $em->persist($histJoueur);

	        try
	        {
		        $em->flush();
		        $this->get('session')->getFlashBag()->add('succes', 'Vos informations ont bien été modifiées');
            }
            catch(\Exception $e)
            {
	            $this->get('session')->getFlashBag()->add('erreur', 'Erreur');
            }

	        // Réinitialise le formulaire
	        $membre = new \UserBundle\Entity\Membre();
	        $form = $this->get('form.factory')->create(ModifProfilType::class, $membre, array(
		        'email' => $this->getUser()->getEmail(),
		        'newsletter' => $this->getUser()->getNewsletter(),
	        ));
        }

	    return $this->render('UserBundle:Utilisateur:modifProfil.html.twig', array(
            'form' => $form->createView()
        ));

    }
}