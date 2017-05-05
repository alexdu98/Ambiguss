<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 02/04/2017
 * Time: 20:38
 */

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExportController extends Controller
{

    public function mainAction ()
    {
	    return $this->render('AmbigussBundle:Export:main.html.twig');
	}

}