<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 18/06/2017
 * Time: 12:36
 */

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class APIController extends Controller
{

	public function getMembreByPseudoOrEmailAction(Request $request)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
		$membres = $repository->getByPseudoOrEmail($request->get('term'));

		return $this->json($membres);
	}
}