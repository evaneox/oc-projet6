<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Spot;
use AppBundle\Form\Type\SpotType;
use AppBundle\Form\Type\SportType;
use AppBundle\Form\Type\SpotFullType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class SpotController extends Controller
{
    /**
     * @Route("/pin/create", name="spot.create")
     * @Method({"GET","POST"})
     */
    public function SpotCreateAction(Request $request)
    {
        // Only user logged in can access to this page
        if(!$this->getUser()){
            return $this->redirectToRoute('fos_user_security_login');
        }

        $spot = new Spot();
        $form = $this->createForm(SpotType::class, $spot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $currentSpot = $this->get('app.spot')->create($spot);
            return $this->redirectToRoute('spot.show' , array(
                'slug' => $currentSpot->getSlug(),
                'id' => $currentSpot->getId()
            ));
        }

        return $this->render('spot/create/create.html.twig', array(
            'form'      => $form->createView(),
        ));
    }

    /**
     * @Route("/pin/listings", name="spot.search")
     * @Method({"GET"})
     */
    public function SpotSearchAction(Request $request)
    {
        $address        = $request->query->get('address');
        $hasLocation    = ($address !== null) ? true : false;

        // Get results only if address exist
        if($hasLocation){
            $form = $this->createForm(SportType::class);
            $spots = $this->get('app.spot')->getResults($address);
        }
        return $this->render('spot/listing/listing.html.twig', array(
            'hasLocation'   => $hasLocation,
            'spots'         => $hasLocation ? $spots : null,
            'form'          => $hasLocation ? $form->createView() : null
        ));
    }

    /**
     * @Route("/pin/show/{slug}/{id}", name="spot.show", requirements={"id": "\d+"})
     * @Method({"GET"})
     */
    public function SpotDetailAction($id)
    {
        // Get current spot
        $spot = $this->getDoctrine()->getRepository('AppBundle:Spot')->findById($id);

        // Return 404 error for wrong spot
        if( empty($id) || !$spot){
            throw $this->createNotFoundException('This spot does not exist');
        }

        return $this->render('spot/show/show.html.twig', array(
            'spot'              => $spot[0],
            'contributions'     => $this->getDoctrine()->getRepository('AppBundle:Contribution')->getUniqueContributionForSpot($id)
        ));
    }

    /**
     * @Route("/pin/edit/{slug}/{id}", name="spot.edit", requirements={"id": "\d+"})
     * @Method({"GET","POST"})
     */
    public function SpotEditAction($id, Request $request)
    {
        // Only user logged in can access to this page
        if(!$this->getUser()){
            return $this->redirectToRoute('fos_user_security_login');
        }

        // Get current spot
        $spotRequest = $this->getDoctrine()->getRepository('AppBundle:Spot')->findById($id);

        // Return 404 error for wrong spot
        if( empty($id) || !$spotRequest){
            throw $this->createNotFoundException('This spot does not exist');
        }
        $spot = $spotRequest[0];
        $form = $this->createForm(SpotFullType::class, $spot);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($form->isValid()){
                $this->get('app.spot')->update($spot);
                return $this->redirectToRoute('spot.show' , array(
                    'id'    => $id,
                    'slug'  => $spot->getSlug()
                ));
            }
        }else{
            // giving editing access only for current user
            $this->get('app.spot')->updateCurrentEditor($spot);
        }

        return $this->render('spot/edit/edit.html.twig', array(
            'spot'              => $spot,
            'form'              => $form->createView(),
            'isWritable'        => $this->get('app.spot')->isWritable($spot),
            'numberImageItems'  => $this->getDoctrine()->getRepository('AppBundle:Image')->getCountImageFomSpot($id),
            'postAction'        => $request->isMethod('POST') ? true : false,
        ));
    }

    /**
     * @Route("/pin/delete/{id}", name="spot.delete", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"POST","DELETE"})
     */
    public function SpotDeleteAction($id, Request $request)
    {
        $this->get('app.spot')->delete($id);
        return $this->redirectToRoute('homepage');
    }
}

