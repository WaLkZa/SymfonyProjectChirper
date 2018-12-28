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
            return $this->redirectToRoute('user_feed');
        } else {
            return $this->redirectToRoute('security_login');
        }
    }
}
