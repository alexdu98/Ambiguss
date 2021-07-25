<?php

namespace App\Controller;

use App\Entity\Membre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MembreController extends AbstractController
{

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function show(Membre $user = null)
    {
        // Il faut être connecté pour consulter des profils
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $em = $this->getDoctrine()->getManager();

        $repoP = $em->getRepository('App:Partie');
        $repoPh = $em->getRepository('App:Phrase');
        $repoB = $em->getRepository('App:Badge');

        $nbPhrases = $repoPh->countAllVisible();

        // Consultation de son propre profil
        if($user == null)
        {
            $nbParties = $repoP->countAllGamesByMembre($this->getUser());
            $bestBadges = $repoB->getBestWinForMembre($this->getUser());
            $nextBadges = $repoB->getNextWinForMembre($this->getUser());

            return $this->render('@FOSUser/Profile/show.html.twig', array(
                'user' => $this->getUser(),
                'nbParties' => $nbParties['nbParties'],
                'nbPhrases' => $nbPhrases['nbPhrases'],
                'bestBadges' => $bestBadges,
                'nextBadges' => $nextBadges
            ));
        }
        // Consultation du profil public d'un membre
        else{
            $nbParties = $repoP->countAllGamesByMembre($user);
            $bestBadges = $repoB->getBestWinForMembre($user);

            return $this->render('User/show_public.html.twig', array(
                'user' => $user,
                'nbParties' => $nbParties['nbParties'],
                'nbPhrases' => $nbPhrases['nbPhrases'],
                'bestBadges' => $bestBadges
            ));
        }
    }

}
