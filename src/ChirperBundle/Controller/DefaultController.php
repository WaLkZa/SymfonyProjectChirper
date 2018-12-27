<?php

namespace ChirperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if ($this->getUser()) {
            //return $this->render('default/index.html.twig');
            return $this->redirectToRoute('chirp_create');
        } else {
            return $this->redirectToRoute('security_login');
        }
        // replace this example code with whatever you need

        //return $this->redirectToRoute('security_login');
    }
}
