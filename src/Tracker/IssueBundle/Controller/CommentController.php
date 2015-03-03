<?php

namespace Tracker\IssueBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\IssueBundle\Entity\Comment;

/**
 * Comment controller.
 *
 * @Route("issue/comment")
 */
class CommentController extends Controller
{

    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}/delete", name="comment_delete")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerIssueBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }
}
