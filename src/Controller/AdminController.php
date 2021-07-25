<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends EasyAdminController
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

    public function statistiques(Request $request){
        $em = $this->getDoctrine();
        $stat = array();

        // Récupération de toutes les statistiques du site
        $stat['visites'] = $em->getRepository('App:Visite')->getStat();
        $stat['membres'] = $em->getRepository('App:Membre')->getStat();
        $stat['phrases'] = $em->getRepository('App:Phrase')->getStat();
        $stat['motsAmbigus'] = $em->getRepository('App:MotAmbigu')->getStat();
        $stat['gloses'] = $em->getRepository('App:Glose')->getStat();
        $stat['parties'] = $em->getRepository('App:Partie')->getStat();
        $stat['reponses'] = $em->getRepository('App:Reponse')->getStat();
        $stat['signalements'] = $em->getRepository('App:Signalement')->getStat();
        $stat['badges'] = $em->getRepository('App:MembreBadge')->getStat();

        return $this->render('Administration/statistiques.html.twig', array(
            'stat' => $stat,
        ));
    }

}
