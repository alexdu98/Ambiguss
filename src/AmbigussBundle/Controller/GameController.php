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
	    $repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');
        $pma = $repo->findByIdPhrase($randlist[0]);
        $phrase = preg_replace('#"#', '\"', $pma[0]->getPhrase()->getContenu());


        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'phrase' => $phrase,
            'MotAmbiguPhrase' => $pma
        ));
    }

}