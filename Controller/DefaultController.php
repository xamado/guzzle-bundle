<?php

namespace Xamado\GuzzleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('XamadoGuzzleBundle:Default:index.html.twig', array('name' => $name));
    }
}
