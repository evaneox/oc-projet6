<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{

    /**
     * @Route("/legacy", name="legacy")
     * @Method({"GET"})
     */
    public function legacyAction()
    {
        return $this->render('legacy.html.twig');
    }

}
