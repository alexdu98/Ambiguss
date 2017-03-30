<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 21/03/2017
 * Time: 09:23
 */

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ClassementController extends Controller{

	public function classementGeneralAction(){
		$repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:Membre');
		$classement = $repository->getClassementGeneral(50);

		return $this->render('AmbigussBundle:Classement:points.html.twig', array (
			'classement' => $classement,
		));
	}

}