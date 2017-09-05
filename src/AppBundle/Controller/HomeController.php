<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $sports = $this->getDoctrine()->getRepository('AppBundle:Sport')->findAll();
        $spots  = $this->getDoctrine()->getRepository('AppBundle:Spot')->findAll();
        $users  = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        return $this->render('homepage.html.twig', array(
            'total_sports'  => sizeof($sports),
            'total_spots'   => sizeof($spots),
            'total_users'   => sizeof($users)
        ));
    }
}
