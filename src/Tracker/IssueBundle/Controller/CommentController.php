<?php

namespace Tracker\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @param Comment $comment
     * @Route("/{comment}/edit", name="issue_comment_edit")
     * @Method("GET")
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @Template()
     * @return array
     */
    public function editAction($comment)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form  = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
            'action' => $this->generateUrl('issue_comment_update', array('comment'=>$comment->getId())),
            'method' => 'PUT'
        ));

        return array(
            'entity' => $comment,
            'edit_form'=> $form->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     * @param $comment Comment
     * @param Request $request
     * @Route("/{comment}", name="issue_comment_update")
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @Method("PUT")
     * @Template("TrackerIssueBundle:Default:edit.html.twig")
     * @return array
     */
    public function updateAction(Request $request, $comment)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
            'action' => $this->generateUrl('issue_comment_update', array('comment'=>$comment->getId())),
            'method' => 'PUT'
        ));
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $comment->getIssue()->getCode())));
        }

        return array(
            'entity'      => $comment,
            'edit_form'   => $editForm->createView()
        );
    }
}
