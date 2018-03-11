<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    private $eventDispatcher;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    public function showAction(Request $request, Membre $user = null)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('form_login');
        }

        $repoPh = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $nbPhrases = $repoPh->countAllVisible();

        $repoP = $this->getDoctrine()->getManager()->getRepository('AppBundle:Partie');

        if($user == null)
        {
            $nbParties = $repoP->countAllGamesByMembre($this->getUser());
            return $this->render('@FOSUser/Profile/show.html.twig', array(
                'user' => $this->getUser(),
                'nbParties' => $nbParties['nbParties'],
                'nbPhrases' => $nbPhrases['nbPhrases'],
            ));
        }
        else{
            $nbParties = $repoP->countAllGamesByMembre($user);

            return $this->render('@App/User/show_public.html.twig', array(
                'user' => $user,
                'nbParties' => $nbParties['nbParties'],
                'nbPhrases' => $nbPhrases['nbPhrases'],
            ));
        }
    }
}