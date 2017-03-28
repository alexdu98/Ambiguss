<?php
/**
 * Created by PhpStorm.
 * User: MELY
 * Date: 3/13/2017
 * Time: 2:32 PM
 */

namespace AmbigussBundle\Controller;


use AmbigussBundle\Entity\Game;
use AmbigussBundle\Form\GameType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class GameController extends  Controller
{
    public function mainAction(Request $request)
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');

        $randlist = array();
        $results = $repository->findall();
        // recup de tous les id dans un array
        foreach ($results as $result){
            array_push($randlist, $result->getId());
        }

        // prendre un id au hasard parmi la liste d'id et récupère son contenu
        shuffle($randlist);

	    $phraseOBJ = $repository->find($randlist[0]);
        $phraseEscape = preg_replace('#"#', '\"', $phraseOBJ->getContenu());

	    $game = new Game();
	    $form = $this->get('form.factory')->create(GameType::class, $game);
	    $form->add('valider', SubmitType::class, array(
	    	'label' => 'Valider',
		    'attr' => array('class' => 'btn btn-primary')
	    ));

	    $form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid()){
		    $data = $form->getData();

		    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');
		    $repository2 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:PoidsReponse');
		    $repository3 = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');

		    $em = $this->getDoctrine()->getManager();
		    foreach($data->reponses as $key => $rep){
			    $rep->setMotAmbiguPhrase($repository->find($request->request->get('ambigussbundle_game')
			                                               ['reponses'][$key]['idMotAmbiguPhrase']));
			    $rep->setContenuPhrase($rep->getMotAmbiguPhrase()->getPhrase()->getContenu());
			    $rep->setValeurMotAmbigu($rep->getMotAmbiguPhrase()->getMotAmbigu()->getValeur());
			    $rep->setValeurGlose($rep->getGlose()->getValeur());
			    if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
				    $rep->setAuteur($this->getUser());
			    }
			    $rep->setPoidsReponse($repository2->findOneByPoidsReponse(1));
			    $rep->setNiveau($repository3->findOneByTitre('Facile'));

			    $em->persist($rep);
		    }

		    $hash = array();
		    $nb_points = 0;
		    $repo4 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
		    foreach($data->reponses as $rep){
		    	$ar = array();
			    $total = 0;
			    foreach($rep->getMotAmbiguPhrase()->getMotAmbigu()->getGloses() as $g){
				    $compteur = $repo4->findByIdPMAetGloses($rep->getMotAmbiguPhrase(), $g->getId());
				    $isSelected = $g->getValeur() == $rep->getValeurGlose() ? true : false;
				    $ar2 = array('nbVotes' => $compteur['nbVotes'], 'isSelected' => $isSelected);
				    $ar[$g->getValeur()] = $ar2;
				    $total = $total + $ar[$g->getValeur()]['nbVotes'];
			    }
			    // Trie le tableau des gloses dans l'ordre décroissant du nombre de réponses
			    uasort($ar, function($a, $b){
				    if ($a['nbVotes'] == $b['nbVotes']) {
					    return 0;
				    }
				    return ($a['nbVotes'] > $b['nbVotes']) ? -1 : 1;
			    });
			    $nb_points = $nb_points + (($ar[$rep->getValeurGlose()]['nbVotes'] / $total) * 100);
			    $hash[$rep->getValeurMotAmbigu()] = $ar;
		    }

		    // Met à jour le nombre de points du joueur
		    if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
			    $this->getUser()->setPointsClassement($this->getUser()->getPointsClassement() + ceil($nb_points));
                $this->getUser()->setCredits($this->getUser()->getCredits() + ceil($nb_points));

                $repNiveau = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
                $scoreNiveauSuivant = $repNiveau->findOneById($this->getUser()->getNiveau()->getId() + 1)->getPointsClassementMin();
                if($this->getUser()->getPointsClassement() >= $scoreNiveauSuivant ){
                    $this->getUser()->setNiveau( $repNiveau->findOneById($this->getUser()->getNiveau()->getId() + 1) );
                }
			    $em->persist($this->getUser());
		    }

		    try{
			    $em->flush();
		    }
		    catch(\Exception $e){
			    $this->get('session')->getFlashBag()->add('erreur', "Erreur insertion");
		    }

		    return $this->render('AmbigussBundle:Game:after_play.html.twig', array (
		    	'phrase' => $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getContenuHTML(),
			    'stats' => $hash, // hashmap de type [motAmbigu => [glose -> nbvotes]]
			    'nb_point' => ceil($nb_points)
		    ));
	    }

	    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');
		$motsAmbigusPhrase = $repository->findByIdPhrase($phraseOBJ->getId());
	    $motsAmbigus = array();
	    for($i = 0; $i < $phraseOBJ->getMotsAmbigusPhrase()->count(); $i++){
			$motsAmbigus[] = array($phraseOBJ->getMotsAmbigusPhrase()->get($i)->getMotAmbigu()->getValeur(),
			                       $motsAmbigusPhrase[$i]->getId());
	    }

        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'form' => $form->createView(),
            'phrase_id' => $phraseOBJ->getId(),
            'motsAmbigus' => json_encode($motsAmbigus),
            'phraseEscape' => $phraseEscape
        ));
    }

}