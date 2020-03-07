<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Form\FOSUser\ProfilEditType;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{

    private $eventDispatcher;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;
    }

    public function showAction(Membre $user = null)
    {
        // Il faut être connecté pour consulter des profils
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $repoPh = $this->getDoctrine()->getManager()->getRepository('AppBundle:Phrase');
        $nbPhrases = $repoPh->countAllVisible();

        $repoP = $this->getDoctrine()->getManager()->getRepository('AppBundle:Partie');

        // Consultation de son propre profil
        if($user == null)
        {
            $nbParties = $repoP->countAllGamesByMembre($this->getUser());
            return $this->render('@FOSUser/Profile/show.html.twig', array(
                'user' => $this->getUser(),
                'nbParties' => $nbParties['nbParties'],
                'nbPhrases' => $nbPhrases['nbPhrases'],
            ));
        }
        // Consultation du profil public d'un membre
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
