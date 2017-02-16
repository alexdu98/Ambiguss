<?php 

namespace TERM1\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class InscriptionController extends Controller
{
    public function indexAction()
    {
	    $content = $this->get("templating")->render('TERM1PlatformBundle:Site:inscription.html.twig');
        return new Response($content);
    }
}

 ?>