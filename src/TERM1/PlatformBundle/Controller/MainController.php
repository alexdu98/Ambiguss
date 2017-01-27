<?php

namespace TERM1\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MainController extends Controller
{
    public function indexAction()
    {
	    $content = $this->get("templating")->render('TERM1PlatformBundle:Default:index.html.twig');
        return new Response($content);
    }
}
