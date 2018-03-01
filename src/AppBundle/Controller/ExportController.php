<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExportController extends Controller
{

    public function mainAction ()
    {
	    return $this->render('AppBundle:Export:main.html.twig');
	}

}