<?php

namespace CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('CommentBundle:Default:index.html.twig');
    }
}
