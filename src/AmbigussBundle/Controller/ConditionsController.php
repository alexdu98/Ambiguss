<?php

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ConditionsController extends Controller
{
    /**
     * @Route("/conditions")
     */
    public function mainAction()
    {
        return $this->render('AmbigussBundle:Default:conditions.html.twig');
    }
}
