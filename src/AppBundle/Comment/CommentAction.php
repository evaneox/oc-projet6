<?php

namespace AppBundle\Comment;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Spot;
use AppBundle\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CommentAction
{
    protected   $em;
    private     $user;

    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->em                           = $entityManager;
        $this->container                    = $container;
        $this->user                         = $container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * Create new comment for spot
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $spotID     = $request->get('spot');
        $content    = trim($request->get('comment'));

        // Be sure user is logged and spot is valid
        if($this->user && preg_match('#^[0-9]$#', $spotID) && !empty($spotID) && !empty($content)){
            $spot = $this->em->getRepository('AppBundle:Spot')->findOneBy(array('id' => $request->get('spot')));

            if(!is_null($spot)){
                $comment = new Comment();
                $comment->setSpot($spot);
                $comment->setAuthor($this->user);
                $comment->setContent($content);
                $this->em->persist($comment);
                $this->em->flush();
            }
        }
    }

    /**
     * Delete a comment
     *
     * @param $id
     */
    public function delete($id)
    {
        if(!empty($id)){
            $comment = $this->em->getRepository('AppBundle:Comment')->findOneBy(array('id' => $id));

            // Be sure this comment exist before remove
            if(!is_null($comment)){
                $this->em->remove($comment);
                $this->em->flush();
            }
        }
    }

}
