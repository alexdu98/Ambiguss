<?php

namespace JudgmentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('JudgmentBundle:Default:index.html.twig');
    }
}
