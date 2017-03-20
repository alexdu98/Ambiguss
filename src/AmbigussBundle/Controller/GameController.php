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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;


class GameController extends  Controller
{
    public function mainAction(Request $request)
    {

        if ($request->getMethod() != 'POST') {

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
        
        }
        else{
            //var_dump($request);
            $tab = $request->request->get("form");
            $id_phrase_recup = $tab["ma1"]["id_phrase"];
            //var_dump($id_phrase_recup);

            $repo = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
            $repo2 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

            $phraseOBJ = $repo->find($id_phrase_recup);
            $pma = $repo2->findByIdPhrase($id_phrase_recup);
            $phraseEscape = preg_replace('#"#', '\"', $phraseOBJ->getContenu());
        }

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
	        ))
            ->add('id_phrase',HiddenType::class, array(
            'data'=>$phraseOBJ->getId()
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
		    //var_dump($data);

		    //construction de la / des reponses
		    $reponse = new \AmbigussBundle\Entity\Reponse();
            //Auteur
		    $reponse->setAuteur($this->getUser()); //Faire un utilisateur fantome pour reponse rentre sans etre connecte 
            //Poid reponse
            $repo3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
		    $reponse->setPoidsReponse($repo3->find(3)); // id de la valeur +1
            //Niveau
            $repo3 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
            $reponse->setNiveau($repo3->find(1)); //facile
            //id glose (pour ma1, faire un une boucle après)
            $tab = $request->request->get("form");
            $id_ma = $tab["ma1"]["valeur"];
            $repo3 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Glose');
            $reponse->setGlose($repo3->find($id_ma));
            //contenu phrase
            //var_dump($phraseOBJ->getContenu());
            $reponse->setContenuPhrase($phraseOBJ->getContenu());
            //valeur mot ambigu
            $reponse->setValeurMotAmbigu($pma[0]->getmotAmbigu()->getValeur());
            //valeur mot ambigi phrase id
            $reponse->setMotAmbiguPhrase($pma[0]);
            //valeur glose
            $reponse->setValeurGlose($repo3->find($id_ma)->getValeur());

		    //ajout du/des reponses dans la bdd
            try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($reponse);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('succes', "La réponse a bien été ajoutée");
                }
                catch(Exception $e){
                    $this->get('session')->getFlashBag()->add('erreur', "Erreur lors de l'insertion de la phrase");
                }


            foreach ($reponse->getMotAmbiguPhrase()->getMotAmbigu()->getGloses() as $g){
                
                $repo4=$this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
                $compteur = $repo4->findByIdPMAetGloses($reponse->getmotAmbiguPhrase(),$g->getId());

                    echo ($compteur[1]+"\n");
            }	    // recuperation des glose dans un array
		    $gloses = null;

		    //recuperation du nombre de vote pour chaque glose, dans le contexte de la phrase
		    $nb_vote = null;

		    //calcul des points obtenu, un simple pourcentage pour commencer : si la reponse represente 75% de tout les votes -> + 75 point.
		    $nb_point = null;

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