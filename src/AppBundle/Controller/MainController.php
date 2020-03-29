<?php

namespace AppBundle\Controller;

use AppBundle\Form\Main\ContactType;
use AppBundle\Service\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{

	public function accueilAction()
	{
		return $this->render('AppBundle:Main:accueil.html.twig');
	}

	public function mentionsAction()
	{
		return $this->render('AppBundle:Main:mentions.html.twig');
	}

	public function conditionsAction()
	{
		return $this->render('AppBundle:Main:conditions.html.twig');
	}

	public function contactAction(Request $request, LoggerInterface $logger)
	{
		$form = $this->get('form.factory')->create(ContactType::class);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			// Vérifie le captcha
			$recaptchaService = $this->get('AppBundle\Service\RecaptchaService');
			$recaptcha = $recaptchaService->check($request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR'));

			// S'il y a eu une erreur avec le captcha
			if(!$recaptcha['success']){
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
                $mailerService = $this->get('AppBundle\Service\MailerService');
                $nbMail = $mailerService->sendEmail(
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

		return $this->render('AppBundle:Main:contact.html.twig', array(
			'form' => $form->createView(),
		));
	}

	public function aProposAction()
	{
	    $githubService = $this->get('AppBundle\Service\GithubService');

		return $this->render('AppBundle:Main:a_propos.html.twig', array(
		    'lastDev' => $githubService->getLastDev(),
            'actualCommit' => $githubService->getActualCommit()
        ));
	}

    public function exportAction ()
    {
        return $this->render('AppBundle:Main:export.html.twig');
    }

}
