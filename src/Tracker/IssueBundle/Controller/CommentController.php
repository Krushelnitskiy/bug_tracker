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
use Tracker\IssueBundle\Entity\Issue;
use Tracker\ProjectBundle\Entity\Project;

/**
 * Comment controller.
 */
class CommentController extends Controller
{
    /**
     * Finds and displays a Comment entity.
     * @param Comment $comment
     * @param Issue $issue
     * @param Project $project
     *
     * @Route("/issue/{issue}/comment/{comment}/edit", name="issue_comment_edit")
     * @Route("/project/{project}/issue/{issue}/comment/{comment}/edit", name="project_issue_comment_edit")
     *
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     *
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array
     */
    public function editAction(Request $request, $comment, Project $project = null)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($project instanceof Project) {
            $routeParam = array('issue' => $comment->getIssue()->getCode(), 'project'=>$project->getCode());
            $editForm = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
                'action' => $this->generateUrl('project_issue_comment_create', $routeParam),
                'method' => 'POST'
            ));
        } else {
            $routeParam = array('issue' => $comment->getIssue()->getCode(), 'comment'=>$comment->getId());
            $editForm = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
                'action' => $this->generateUrl('issue_comment_edit', $routeParam),
                'method' => 'POST'
            ));
        }

        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isValid()) {
            $em->flush();

            if ($project instanceof Project) {
                $routeParam = array('project'=>$project->getCode(), 'issue' => $comment->getIssue()->getCode());
                return $this->redirect($this->generateUrl('project_issue_show', $routeParam));
            } else {
                $routeParam = array('issue' => $comment->getIssue()->getCode());
                return $this->redirect($this->generateUrl('issue_show', $routeParam));
            }
        }

        return array(
            'project' => $project,
            'entity' => $comment,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     *
     * @param Request $request
     * @param Issue $issue
     * @param Project $project
     *
     * @Route("/issue/{issue}/comment", name="issue_comment_create")
     * @Route("/project/{project}/issue/{issue}/comment", name="project_issue_comment_create")
     *
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     *
     * @Method("POST")
     * @Template("TrackerIssueBundle:Issue:edit.html.twig")
     *
     * @return array
     */
    public function createCommentAction(Request $request, Issue $issue, Project $project = null)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Comment();
        $form = $this->createCreateCommentForm($entity, $issue->getCode(), $project);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreated(new \DateTime());
            $author = $this->getUser();
            $entity->setAuthor($author);
            $entity->setIssue($issue);

            $em->persist($entity);
            $em->flush();

            if ($project instanceof Project) {
                $routeParam = array('project'=>$project->getCode(), 'issue' => $issue->getCode());
                return $this->redirect($this->generateUrl('project_issue_show', $routeParam));
            } else {
                $routeParam = array('issue' => $entity->getIssue()->getCode());
                return $this->redirect($this->generateUrl('issue_show', $routeParam));
            }
        }

        return array(
            'entity' => $entity->getIssue(),
            'comment_form' => $form->createView()
        );
    }

    /**
     * Deletes a Issue entity.
     *
     * @param Issue $issue
     * @param Comment $comment
     * @param Project $project
     *
     * @Route("/issue/{issue}/comment/{comment}", name="issue_comment_delete")
     * @Route("/project/{project}/issue/{issue}/comment/{comment}", name="project_issue_comment_delete")
     *
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     *
     * @return array
     */
    public function deleteCommentAction($issue, $comment, Project $project = null)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('delete', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        if ($project instanceof Project) {
            $routeParam = array('project'=>$project->getCode(), 'issue' => $issue->getCode());
            return $this->redirect($this->generateUrl('project_issue_show', $routeParam));
        } else {
            $routeParam = array('issue' => $issue->getCode());
            return $this->redirect($this->generateUrl('issue_show', $routeParam));
        }
    }

    /**
     * @param Comment $comment
     * @param integer $issueId
     * @param Project $project
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateCommentForm(Comment $comment, $issueId, Project $project)
    {
        if ($project instanceof Project) {
            $routeParams = array('issue' => $issueId, 'project'=>$project->getCode());
            $form = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
                'action' => $this->generateUrl('project_issue_comment_create', $routeParams),
                'method' => 'POST'
            ));
        } else {
            $form = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
                'action' => $this->generateUrl('issue_comment_create', array('issue' => $issueId)),
                'method' => 'POST'
            ));
        }


        return $form;
    }
}
