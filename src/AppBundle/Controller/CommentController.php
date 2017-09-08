<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Spot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



class CommentController extends Controller
{
    /**
     * @Route("/comment/create", name="comment.create")
     * @Method({"POST"})
     */
    public function CommentCreateAction(Request $request)
    {
        $this->get('app.comment')->create($request);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/comment/delete/{id}", name="comment.delete", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"POST","DELETE","GET"})
     */
    public function CommentDeleteAction($id, Request $request)
    {
        $this->get('app.comment')->delete($id);
        return $this->redirect($request->headers->get('referer'));
    }
}

