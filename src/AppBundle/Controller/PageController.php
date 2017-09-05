<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{

    /**
     * @Route("/legacy", name="legacy")
     */
    public function legacyAction()
    {
        return $this->render('legacy.html.twig');
    }


    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction()
    {
        return $this->render('faq.html.twig');
    }

}
