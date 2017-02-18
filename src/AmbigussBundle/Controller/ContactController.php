<?php

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact")
     */
    public function mainAction()
    {
        return $this->render('AmbigussBundle:Default:contact.html.twig');
    }
}
