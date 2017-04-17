<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\ModifProfilType;
class UtilisateurController extends Controller
{
	public function profilAction(Request $request, \UserBundle\Entity\Membre $user=null)
	{
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('user_connexion');
		}
        if($user==null)
		    return $this->render('UserBundle:Utilisateur:profil.html.twig',array (
                'user' => $this->getUser(),));
		else
            return $this->render('UserBundle:Utilisateur:profil.html.twig',array (
                'user' => $user,));
	}

    public function ModifProfilAction(Request $request){

        $membre = new \UserBundle\Entity\Membre();
        $form = $this->get('form.factory')->create(ModifProfilType::class, $membre, array(
            'pseudo'=> $this->getUser()->getPseudo(),
            'email' =>$this->getUser()->getEmail(),
        ));


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ) {

            $data = $form->getData();


            $repoUser = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
            //$membre = null;

            $membre= $repoUser->find($this->getUser()->getid());
            if($data->getEmail()!=null)
                $membre->setEmail($data->getEmail());
            if($data->getMdp()!=null)
            {
                // Hash le Mdp
                $encoder = $this->get('security.password_encoder');
                $hash = $encoder->encodePassword($membre, $data->getMdp());

                $membre->setMdp($hash);
            }
            try {
                 $em = $this->getDoctrine()->getManager();
                 $em->persist($membre);
                 $em->flush();}
            catch (Exception $e){}

            $this->get('session')->getFlashBag()->add('succes', 'Vos informations ont bien été modifiées');
            return $this->render('UserBundle:Utilisateur:modifProfil.html.twig', array(
                'form' => $form->createView()
            ));
           // return $this->redirectToRoute('user_deconnexion');
        }
        return $this->render('UserBundle:Utilisateur:modifProfil.html.twig', array(
            'form' => $form->createView()
        ));

    }
}