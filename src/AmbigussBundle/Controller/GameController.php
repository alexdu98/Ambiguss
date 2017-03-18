<?php
/**
 * Created by PhpStorm.
 * User: MELY
 * Date: 3/13/2017
 * Time: 2:32 PM
 */

namespace AmbigussBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;


class GameController extends  Controller
{
    public function mainAction(Request $request)
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

        //recupere les gloses dans un array
        foreach ($pma  as $map){
            foreach ($map->getmotAmbigu()->getGloses() as $g) {
                $gloses[] =array($g->getValeur()=>"v");
            }
        }


        $ma= new \AmbigussBundle\Entity\MotAmbiguPhrase();


        $bigformBuilder = $this->createFormBuilder();
        $i=1;
        foreach ($pma  as $map){
            $formBuilder = $this->get('form.factory')->createNamedBuilder($i,FormType::class, $ma->getmotAmbigu());
           $formBuilder
               ->add('valeur',ChoiceType::class , array(
                   'choices' =>  $gloses,
                   'required'    => false,
                   'placeholder' => "Choisissez une glose",
                   'label' =>$map->getmotAmbigu()->getValeur(),
                   'empty_data'  => null
               ));

           // evenement au moment de la selection de "autre" (code incorrecte
            /*->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {echo  "gg";
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data) {
                    return;
                }
                if ($data['valeur']== "Autre") {
                    $form->add('Nouvelle glose', TextType::class);}
                }
            ); */
            $bigformBuilder->add($formBuilder); $i++;
        }


        $form = $bigformBuilder->getForm();
        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'phrase' => $phrase,
            'MotAmbiguPhrase' => $pma,
            'form' => $form->createView(),
        ));

        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            $data = $form->getData();
            var_dump($data);

            //construction de la / des reponses
            $reponse = new \AmbigussBundle\Entity\Reponse();
            $reponse->setAuteur($this->getUser()); //Faire un utilisateur fantome pour reponse rentre sans etre connecte ?
            $reponse->setPhrase($randlist[0]);
            $reponse->setPoidsReponse(3); // id de la valeur +1


            //ajout du/des reponses dans la bdd


            // recuperation des glose dans un array
            $gloses;

            //recuperation du nombre de vote pour chaque glose, dans le contexte de la phrase
            $nb_vote;

            //calcul des points obtenu, un simple pourcentage pour commencer : si la reponse represente 75% de tout les votes -> + 75 point. 
            $nb_point;

            return $this->render('AmbigussBundle:Game:after_play.html.twig', array (
                    'gloses' => $gloses,
                    'nb_vote' => $nb_vote,
                    'nb_point' => $nb_point
                )); 
        }
    }

}