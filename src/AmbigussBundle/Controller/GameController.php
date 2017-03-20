<?php
/**
 * Created by PhpStorm.
 * User: MELY
 * Date: 3/13/2017
 * Time: 2:32 PM
 */

namespace AmbigussBundle\Controller;


use AmbigussBundle\Form\ReponseType;
use AmbigussBundle\Repository\GloseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;


class GameController extends  Controller
{
    public function mainAction(Request $request)
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
        //$rnd=rand(1,15);
        $randlist = array();
        $results = $repository->findall();
        // recup de tous les id dans un array
        foreach ($results as $result){
            array_push($randlist,$result->getId());
        }

        // prendre un id au hasard parmi la liste d'id et récupère son contenu
        shuffle($randlist);
	    $repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
	    $repo2 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

	    $phraseOBJ = $repo->find($randlist[0]);
		$pma = $repo2->findByIdPhrase($randlist[0]);
        $phraseEscape = preg_replace('#"#', '\"', $phraseOBJ->getContenu());

	    $bigformBuilder = $this->createFormBuilder();

        $i = 1;
	    $glosesTable = array();

        foreach ($pma  as $map){
			$gloses = array();
	        foreach ($map->getmotAmbigu()->getGloses() as $g) {
		        $gloses[$g->getValeur()] = $g->getValeur();
	        }
	        $glosesTable[] = $gloses;

	        $ma = $map->getmotAmbigu()->getValeur();

        	$formBuilder = $this->get('form.factory')->createNamedBuilder('ma' . $i, FormType::class);
	        $formBuilder->add('valeur', EntityType::class , array(
	        	'class' => 'AmbigussBundle\Entity\Glose',
		        'choice_label' =>  'valeur',
		        'required'    => false,
		        'placeholder' => "Choisissez une glose",
		        'label' => $ma,
		        'empty_data'  => null,
		        'mapped' => false,
		        'query_builder' => function(GloseRepository $repo) use ($ma){
	        		return $repo->findGlosesValueLinkedByMotAmbiguValue($ma);
	        }
	        ));

            $bigformBuilder->add($formBuilder);
            $i++;
        }

	    $bigformBuilder->add('Valider', SubmitType::class, array(
	    	'attr' => array('class' => 'btn btn-primary')
	    ));

        $form = $bigformBuilder->getForm();

	    $form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid()){
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

        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'phrase' => $phraseOBJ,
            'phraseEscape' => $phraseEscape,
            'form' => $form->createView(),
        ));
    }

}