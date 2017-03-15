<?php
/**
 * Created by PhpStorm.
 * User: MELY
 * Date: 3/13/2017
 * Time: 2:32 PM
 */

namespace AmbigussBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GameController extends  Controller
{
    public function mainAction()
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
        //$rnd=rand(1,15);
        $randlist=array();
        $results = $repository->findall();
        // recup de tous les id dans un array
        foreach ($results as $result){
            array_push($randlist,$result->getId());

        }

        // prendre un id au hasard parmi la liste d'id et récupère son contenu
        shuffle($randlist);
        $phrase= $repository->find($randlist[0])->getContenu();
        $phrase = preg_replace('#"#', '\"', $phrase);

        //on récupère les mots ambigus de la phrase

        $motsambigus=$repository->find($randlist[0])->getMotsAmbigus();
        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'phrase' => $phrase,
            'motAmbigus' => $motsambigus->getVAlues(),
        ));
    }

}