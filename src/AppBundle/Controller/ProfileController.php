<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class ProfileController extends BaseController
{
    /**
     * Show the user.
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user'          => $user,
            'contribution'  =>  sizeof($this->getDoctrine()->getRepository('AppBundle:Contribution')->findByContributor($user->getId())),
            'comment'       =>  sizeof($this->getDoctrine()->getRepository('AppBundle:Comment')->findByAuthor($user->getId()))
        ));
    }
}