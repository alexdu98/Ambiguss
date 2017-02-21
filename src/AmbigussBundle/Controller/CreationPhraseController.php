<?php

namespace AmbigussBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CreationPhraseController extends Controller
{
    /**
     * @Route("/")
     */
    public function mainAction()
    {
        return $this->render('AmbigussBundle:Default:creationPhrase.html.twig');
    }
}
