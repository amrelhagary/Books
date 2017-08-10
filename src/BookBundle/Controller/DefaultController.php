<?php

namespace BookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function defaultAction()
    {
        return $this->render('Default/index.html.twig');
    }
}