<?php

namespace AppBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseAdminController
{

    public function createNewMembreEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function updateMembreEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::updateEntity($user);
    }

    public function persistMembreEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::persistEntity($user);
    }

    public function statistiquesAction(Request $request){
        $em = $this->getDoctrine();
        $stat = array();

        // Récupération de toutes les statistiques du site
        $stat['visites'] = $em->getRepository('AppBundle:Visite')->getStat();
        $stat['membres'] = $em->getRepository('AppBundle:Membre')->getStat();
        $stat['phrases'] = $em->getRepository('AppBundle:Phrase')->getStat();
        $stat['motsAmbigus'] = $em->getRepository('AppBundle:MotAmbigu')->getStat();
        $stat['gloses'] = $em->getRepository('AppBundle:Glose')->getStat();
        $stat['parties'] = $em->getRepository('AppBundle:Partie')->getStat();
        $stat['reponses'] = $em->getRepository('AppBundle:Reponse')->getStat();
        $stat['signalements'] = $em->getRepository('AppBundle:Signalement')->getStat();

        return $this->render('@App/Administration/statistiques.html.twig', array(
            'stat' => $stat,
        ));
    }

}
