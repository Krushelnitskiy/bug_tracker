<?php

namespace Tracker\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Comment controller.
 *
 * @Route("issue/comment")
 */
class CommentController extends Controller
{

    /**
     * Finds and displays a Comment entity.
     * @param integer $id
     * @Route("/{id}/delete", name="comment_delete")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerIssueBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        return array(
            'entity' => $entity
        );
    }
}
