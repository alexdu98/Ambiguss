<?php

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MentionsController extends Controller
{
    /**
     * @Route("/mentions")
     */
    public function mainAction()
    {
        return $this->render('AmbigussBundle:Default:mentions.html.twig');
    }
}
