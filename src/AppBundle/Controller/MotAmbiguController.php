<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MotAmbiguController extends Controller
{
    public function moderation_maAction()
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:MotAmbigu');
                $ma= $repo->getSignale(array(
                    'signale' => true,
                ));


                return $this->render('AppBundle:MAmbigu:getAll.html.twig', array(
                    'mambigus' => $ma,
                ));
            }
            else
            {
	            $this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

	            return $this->redirectToRoute('fos_user_security_login');
            }
        }
	    throw $this->createAccessDeniedException();
    }

    public function keepAction(\AppBundle\Entity\MotAmbigu $mamb)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_MODERATEUR'))
        {
            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
            {
                try
                {
                    $mamb->setSignale(0);
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($mamb);
                    $em->flush();

                    return $this->json(array(
                        'succes' => true,
                    ));
                }
                catch(\Exception $e)
                {
                    return $this->json(array(
                        'succes' => false,
                        'message' => $e,
                    ));
                }
            }
            else
            {
	            $this->get('session')->getFlashBag()->add('erreur', "L'accès à la modération nécessite d'être connecté sans le système d'auto-connexion.");

	            return $this->redirectToRoute('fos_user_security_login');
            }
        }
	    throw $this->createAccessDeniedException();
    }



}
