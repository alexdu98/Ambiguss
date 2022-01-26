<?php

namespace App\Controller;

use App\Form\Main\ContactType;
use App\Service\GithubService;
use App\Service\RecaptchaService;
use App\Service\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class MainController extends AbstractController
{

    private $request;
    private $logger;
    private $recaptcha;
    private $mailer;

    public function __construct(RequestStack $request, LoggerInterface $logger, RecaptchaService $recaptcher, MailerService $mailer)
    {
        $this->request = $request;
        $this->logger = $logger;
        $this->recaptcher = $recaptcher;
        $this->mailer = $mailer;
    }

	public function accueil()
	{
		return $this->render('Main/accueil.html.twig');
	}

	public function mentions()
	{
		return $this->render('Main/mentions.html.twig');
	}

	public function conditions()
	{
		return $this->render('Main/conditions.html.twig');
	}

	public function contact()
	{
		$form = $this->get('form.factory')->create(ContactType::class);

		$form->handleRequest($this->request->getCurrentRequest());

		if($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			// Vérifie le captcha
			$recaptcha = $this->recaptcher->check($this->request->getCurrentRequest()->get('g-recaptcha-response'), $this->request->getCurrentRequest()->server->get('REMOTE_ADDR'));

			// S'il y a eu une erreur avec le captcha
			if(isset($recaptcha['success']) && !$recaptcha['success']){
                $message = implode('<br>', $recaptcha['error-codes']);
                $this->get('session')->getFlashBag()->add('danger', $message);

                $logInfos = array(
                    'msg' => $message,
                    'user' => $this->getUser() ? $this->getUser()->getUsername() : 'non connecté',
                    'ip' => $request->server->get('REMOTE_ADDR')
                );
                $logger->error(json_encode($logInfos));
            }
            else {

			    // Si c'est un membre, on récupère son pseudo et son email
                if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
                {
                    $data['pseudo'] = $this->getUser()->getUsername();
                    $data['email'] = $this->getUser()->getEmail();
                }

                // On vérifie les champs obligatoires
			    if(empty($data['message']) || empty($data['pseudo']) || empty($data['email'])){
                    $this->get('session')->getFlashBag()->add('danger', "Tous les champs ne sont pas renseignés.");
                }

                // On envoie le mail de contact
                $nbMail = $this->mailer->sendEmail(
                    MailerService::CONTACT,
                    array(
                        'pseudoExpediteur' => $data['pseudo'],
                        'emailExpediteur' => $data['email'],
                        'message' => $data['message'],
                    )
                );

                // Vérification de l'envoie de l'email
                if($nbMail === 0)
                    $this->get('session')->getFlashBag()->add('danger', "L'envoi de l'email a échoué.");
                else
                    $this->get('session')->getFlashBag()->add('success', 'Formulaire de contact envoyé. Nous vous répondrons dès que possible.');

                // rediriger vers la page de contact (pour vider le formulaire)
                return $this->redirectToRoute('contact_show', ['_fragment' => 'formulaireContact']);
			}
		}

		return $this->render('Main/contact.html.twig', array(
			'form' => $form->createView(),
		));
	}

	public function aPropos(GithubService $githubService)
	{
		return $this->render('Main/a_propos.html.twig', array(
		    'lastDev' => $githubService->getLastDev(),
            'actualCommit' => $githubService->getActualCommit()
        ));
	}

    public function export()
    {
        return $this->render('Main/export.html.twig');
    }

}
