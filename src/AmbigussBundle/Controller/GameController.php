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
use AmbigussBundle\Form\GloseAddType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class GameController extends  Controller
{
    public function mainAction(Request $request)
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Phrase');
	    $repmap = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');

	    $phrases = null;
	    if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
		    $phrases = $repmap->findIdPhrasesNotPlayedByMembre($this->getUser());
	    }
	    else{
		    $date = new \DateTime();
		    // Date d'il y a 3 jours
		    $date = $date->getTimestamp() - (3600 * 24 * 30);
		    $phrases = $repmap->findIdPhrasesNotPlayedByIpSince($_SERVER['REMOTE_ADDR'], $date);
	    }

	    // Si toutes les phrases ont été joués
	    $allPhrasesPlayed = false;
	    if(empty($phrases)){
		    $allPhrasesPlayed = true;
			$phrases = $repmap->findIdPhrasesByLessNumberReponse(25);
	    }

	    // Rend une clé au hasard
	    $phrase_id = array_rand($phrases);

	    $phraseOBJ = $repository->find($phrases[$phrase_id][1]);

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
		    $valid = true;
		    foreach($data->reponses as $key => $rep){
		    	if(!$rep->getGlose()){
		    		$valid = false;
		    		break;
			    }
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

		    // Si tous les mots ambigus ont une glose associée
		    if($valid){
			    $hash = array();
			    $map = array();
			    $nb_points = 0;
			    $repo4 = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:Reponse');
			    foreach($data->reponses as $rep){
				    $map[] = $rep->getMotAmbiguPhrase()->getId();
				    $gloses = array();
				    $total = 0;
				    foreach($rep->getMotAmbiguPhrase()->getMotAmbigu()->getGloses() as $g){
					    $compteur = $repo4->findByIdPMAetGloses($rep->getMotAmbiguPhrase(), $g->getId());
					    $isSelected = $g->getValeur() == $rep->getValeurGlose() ? true : false;
					    $ar2 = array(
						    'nbVotes'    => $compteur['nbVotes'],
						    'isSelected' => $isSelected
					    );
					    $gloses[$g->getValeur()] = $ar2;
					    $total = $total + $gloses[$g->getValeur()]['nbVotes'];
				    }
				    // Trie le tableau des gloses dans l'ordre décroissant du nombre de réponses
				    uasort($gloses, function($a, $b){
					    if($a['nbVotes'] == $b['nbVotes']){
						    return 0;
					    }
					    return ($a['nbVotes'] > $b['nbVotes']) ? -1 : 1;
				    });
				    $resMA = array(
					    'valeurMA' => $rep->getValeurMotAmbigu(),
					    'gloses'   => $gloses
				    );
				    if($total > 0){
					    $nb_points = $nb_points + (($gloses[$rep->getValeurGlose()]['nbVotes'] / $total) * 100);
				    }
				    $hash[$rep->getMotAmbiguPhrase()->getOrdre()] = $resMA;
			    }

			    $alreadyPlayed = false;
			    if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
				    $rep = $repo4->findBy(array(
					    'motAmbiguPhrase' => $map,
					    'auteur'          => $this->getUser()
				    ));
				    // Si le joueur n'avait pas déjà joué la phrase on lui ajoute les points
				    if(!$rep){
					    $this->getUser()->setPointsClassement($this->getUser()->getPointsClassement() + ceil($nb_points));
					    $this->getUser()->setCredits($this->getUser()->getCredits() + ceil($nb_points));

					    $repNiveau = $this->getDoctrine()->getManager()->getRepository('UserBundle:Niveau');
					    $scoreNiveauSuivant = $repNiveau->findOneById($this->getUser()->getNiveau()->getId() + 1)->getPointsClassementMin();
					    if($this->getUser()->getPointsClassement() >= $scoreNiveauSuivant){
						    $this->getUser()->setNiveau($repNiveau->findOneById($this->getUser()->getNiveau()->getId() + 1));
					    }
					    $em->persist($this->getUser());
				    }
				    else{
					    $alreadyPlayed = true;
				    }
			    }

			    try{
				    $em->flush();
			    }
			    catch(\Exception $e){
				    $this->get('session')->getFlashBag()->add('erreur', "Erreur insertion");
			    }

			    $this->get('session')->getFlashBag()->add('phrase', $data->reponses->get(1)->getMotAmbiguPhrase()->getPhrase()->getContenuHTML());
			    $this->get('session')->getFlashBag()->add('stats', $hash);
			    $this->get('session')->getFlashBag()->add('alreadyPlayed', $alreadyPlayed);
			    $this->get('session')->getFlashBag()->add('nb_points', ceil($nb_points));

			    return $this->redirectToRoute('ambiguss_game_result');
		    }
		    else{
			    $this->get('session')->getFlashBag()->add('erreur', "Tous les mots ambigus doivent avoir une glose");
		    }
	    }

	    $repository = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:MotAmbiguPhrase');
		$motsAmbigusPhrase = $repository->findByIdPhrase($phraseOBJ->getId());
	    $motsAmbigus = array();
	    for($i = 0; $i < $phraseOBJ->getMotsAmbigusPhrase()->count(); $i++){
			$motsAmbigus[] = array(
				$phraseOBJ->getMotsAmbigusPhrase()->get($i)->getMotAmbigu()->getValeur(),
				$motsAmbigusPhrase[$i]->getId(),
			    $phraseOBJ->getMotsAmbigusPhrase()->get($i)->getOrdre()
			);
	    }

	    // récupérer tous les likes d'un utilisateur
	    $likesArray = array();
	    if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')){
		    $rep = $this->getDoctrine()->getManager()->getRepository('AmbigussBundle:AimerPhrase');
		    $likesUser = $rep->findBymembre($this->getUser());
		    foreach($likesUser as $like){
			    array_push($likesArray, $like->getPhrase()->getId());
		    }
	    }

	    $glose = new \AmbigussBundle\Entity\Glose();
	    $addGloseForm = $this->get('form.factory')->create(GloseAddType::class, $glose, array('action' =>
		                                                                                          $this->generateUrl
		                                                                                          ('ambiguss_glose_add')));

        return $this->render('AmbigussBundle:Game:play.html.twig', array(
            'form' => $form->createView(),
            'phrase_id' => $phraseOBJ->getId(),
            'motsAmbigus' => json_encode($motsAmbigus),
            'phraseEscape' => $phraseEscape,
            'likes' => $likesArray,
            'alreadyPlayed' => $allPhrasesPlayed,
            'addGloseForm' => $addGloseForm->createView()
        ));
    }

    public function resultatAction(Request $request){
	    $phrase = $this->get('session')->getFlashBag()->get('phrase');
	    $stats = $this->get('session')->getFlashBag()->get('stats');
	    $alreadyPlayed = $this->get('session')->getFlashBag()->get('alreadyPlayed');
	    $nb_points = $this->get('session')->getFlashBag()->get('nb_points');

    	if(!empty($phrase) && !empty($stats) && !empty($nb_points)){
		    return $this->render('AmbigussBundle:Game:after_play.html.twig', array(
			    'phrase'   => $phrase[0],
			    'stats'    => $stats[0],
			    'alreadyPlayed' => $alreadyPlayed,
			    'nb_point' => $nb_points[0]
		    ));
	    }
	    throw $this->createNotFoundException();
    }

    public function getUnusedId($allId, $usedId){
        $unusedId= array();
        $find=0;
        foreach($allId as $A){
            foreach ($usedId as $U){
                if ($A== $U){
                    $find=1;
                }
            }
            if($find==0){
                array_push($unusedId, $A);
            }
            else{
                $find=0;
            }
        }
        return $unusedId;
}
}