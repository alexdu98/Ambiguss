<?php

namespace AppBundle\Controller;

use AppBundle\Form\Main\ContactType;
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

	public function contactAction(Request $request)
	{
		$form = $this->get('form.factory')->create(ContactType::class);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid())
		{
			$data = $form->getData();

			$recaptcha = $this->get('AppBundle\Service\Recaptcha');
			$recap = $recaptcha->check($request->request->get('g-recaptcha-response'));
			if($recap->succes)
			{
				if(!empty($data['message']))
				{
					if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
					{
						$data['pseudo'] = $this->getUser()->getUsername();
						$data['email'] = $this->getUser()->getEmail();
					}

					if(!empty($data['pseudo']) && !empty($data['email']))
					{
                        // Envoi de l'email de contact
                        $message = (new \Swift_Message())
                            ->setSubject('[Ambiguss] Contact')
                            ->setFrom($this->getParameter('emailFrom'))
                            ->setTo($this->getParameter('emailContact'))
                            ->setBody($this->renderView('AppBundle:Mail:contact.html.twig', array(
                                'recipient' => $this->getDoctrine()->getRepository('AppBundle:Membre')->findOneByUsernameCanonical('alex'),
                                'subject' => '[Ambiguss] Contact',
                                'pseudoExpediteur' => $data['pseudo'],
                                'emailExpediteur' => $data['email'],
                                'message' => $data['message'],
                            )), 'text/html');

						if($this->get('mailer')->send($message))
						{
							$this->get('session')->getFlashBag()->add('succes', 'Formulaire de contact envoyé. Nous vous répondrons dès que possible.');
						}
						else
						{
							$this->get('session')->getFlashBag()->add('erreur', "L'envoi de l'email a échoué.");
						}

						// rediriger vers la page de contact (pour vider le formulaire)
						return $this->redirectToRoute('app_contact', ['_fragment' => 'formulaireContact']);
					}
					else
					{
						$this->get('session')->getFlashBag()->add('erreur', "Le pseudo et l'email ne doivent pas être vide.");
					}
				}
				else
				{
					$this->get('session')->getFlashBag()->add('erreur', "Le message ne peut pas être vide.");
				}
			}
			else
			{
				$erreurStr = "";
				foreach($recap->erreurs as $erreur)
				{
					$erreurStr .= $erreur;
				}
				$this->get('session')->getFlashBag()->add('erreur', $erreurStr);
			}
		}

		return $this->render('AppBundle:Main:contact.html.twig', array(
			'form' => $form->createView(),
		));
	}

	public
	function aProposAction()
	{
		return $this->render('AppBundle:Main:a_propos.html.twig');
	}
}
