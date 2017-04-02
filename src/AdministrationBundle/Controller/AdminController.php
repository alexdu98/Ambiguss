<?php

namespace AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{

	public function mainAction()
	{
		if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'))
		{
			throw $this->createAccessDeniedException();
		}

		$stat = array();

		return $this->render('AdministrationBundle:Administration:accueil.html.twig', array(
			'stat' => $stat,
		));
	}
}