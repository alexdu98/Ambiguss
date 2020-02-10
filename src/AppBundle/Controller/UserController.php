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

    public function editAction(Request $request)
    {
        // Il faut être connecté pour modifier son profil
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('form.factory')->create(ProfilEditType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            // On met à jour le membre
            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function registerAction(Request $request, LoggerInterface $logger)
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('fos_user.registration.form.factory')->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Vérifie le captcha
            $recaptchaService = $this->get('AppBundle\Service\RecaptchaService');
            $recaptcha = $recaptchaService->check($request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR'));

            // S'il y a eu une erreur avec le captcha
            if(!$recaptcha['success']){
                $message = implode('<br>', $recaptcha['error-codes']);
                $this->get('session')->getFlashBag()->add('danger', $message);

                $logInfos = array(
                    'msg' => $message,
                    'user' => $this->getUser()->getId() ?? 'non connecté',
                    'ip' => $request->server->get('REMOTE_ADDR')
                );
                $logger->error(json_encode($logInfos));
            }
            else if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function checkEmailAction(Request $request, LoggerInterface $logger)
    {
        $email = $request->getSession()->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('fos_user_registration_register'));
        }

        $request->getSession()->remove('fos_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        $flashBag = $this->get('session')->getFlashBag();
        $flashBag->clear();
        if (null === $user) {
            $msg = 'Inscription échouée, veuillez réessayer ou contacter un administrateur si cela persiste.';
            $flashBag->add('danger', $msg);

            $logInfos = array(
                'msg' => $msg,
                'email' => $email,
                'ip' => $request->server->get('REMOTE_ADDR')
            );
            $logger->critical(json_encode($logInfos));
        }
        else {
            $flashBag->add('info', 'Inscription réussie, veuillez cliquer sur le lien de confirmation envoyé par email à l\'adresse ' . $email . '.');
        }

        return $this->redirectToRoute('fos_user_security_login');
    }

    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_security_login');
            $response = new RedirectResponse($url);
        }

        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        $flashBag = $this->get('session')->getFlashBag();
        $flashBag->clear();
        $flashBag->add('success', 'Inscription confirmée, vous pouvez vous connecter.');

        return $response;
    }

    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);
        $flashBag = $this->get('session')->getFlashBag();
        $flashBag->clear();

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null !== $user && !$user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.retry_ttl'))) {
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->get('fos_user.util.token_generator')->generateToken());
            }

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            $flashBag->add('success', 'Veuillez cliquer sur le lien de réinitialisation de mot de passe envoyé par email à l\'adresse ' . $user->getEmailCanonical() . '.');

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }
        else if (!$user) {
            $flashBag->add('danger', 'Aucun utilisateur trouvé.');
        }
        else if ($user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.retry_ttl'))) {
            $lastReq = $user->getPasswordRequestedAt()->format('H\hi');
            $nextReqTS = $user->getPasswordRequestedAt()->getTimestamp() + $this->getParameter('fos_user.resetting.retry_ttl');
            $nextReq = (new \DateTime())->setTimestamp($nextReqTS)->format('H\hi');

            $msg = 'Un email a déjà été envoyé à ' . $lastReq . ' à l\'adresse ' . $user->getEmailCanonical() . '.<br>Merci d\'attendre ' . $nextReq . '.';
            $flashBag->add('danger', $msg);
        }

        return new RedirectResponse($this->generateUrl('fos_user_security_login'));
    }

}
