<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

    public function contactAction()
    {
        return $this->render('AppBundle:Main:contact.html.twig');
    }

	public function aProposAction()
	{
		return $this->render('AppBundle:Main:apropos.html.twig');
	}
}
