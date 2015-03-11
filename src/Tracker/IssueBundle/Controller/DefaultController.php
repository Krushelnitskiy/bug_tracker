<?php

namespace Tracker\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Validator\Constraints\DateTime;
use Tracker\IssueBundle\Entity\Comment;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\IssueBundle\Entity\Status;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Tracker\IssueBundle\Security\Authorization\Voter\IssueVoter;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Type;

/**
 * Issue controller.
 *
 * @Route("/issue")
 */
class DefaultController extends Controller
{

    /**
     * Lists all Issue entities.
     *
     * @Route("/", name="issue")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('view', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TrackerIssueBundle:Issue')->findAll();

        return array(
            'entities' => $entities,
            'emptyEntity' => new Issue()
        );
    }
    /**
     * Creates a new Issue entity.
     * @param Request $request
     * @Route("/", name="issue_create")
     * @Method("POST")
     * @Template("TrackerIssueBundle:Default:new.html.twig")
     * @return array
     */
    public function createAction(Request $request)
    {

        if (false === $this->get('security.authorization_checker')->isGranted('create', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();

        /**
         * @var $user User
         */
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $projects = array();
        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
            $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
        }

        $form  = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('issue_create'),
            'method' => 'POST',
            'projects' => $projects
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreated(new \DateTime());
            $entity->setUpdated(new \DateTime());

            $entityStatus = $em->getRepository('TrackerIssueBundle:Status')->findByValue(Status::STATUS_OPEN);
            $entity->setStatus($entityStatus[0]);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/new", name="issue_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $projects = array();
        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
            $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
        }

        $form  = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('issue_create'),
            'method' => 'POST',
            'projects' => $projects
        ));

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a Issue entity.
     * @param integer $id
     * @Route("/{id}", name="issue_show")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TrackerIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        if (false === $this->get('security.authorization_checker')->isGranted('view', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $createCommentForm = $this->createCreateCommentForm(new Comment(), $id);
        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByIssue($entity);

        return array(
            'entity'      => $entity,
            'activity'      => $activity,
            'comment_form' => $createCommentForm->createView()
        );
    }

    /**
     * Displays a form to edit an existing Issue entity.
     * @param integer $id
     * @Route("/{id}/edit", name="issue_edit")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TrackerIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        if (false === $this->get('security.authorization_checker')->isGranted('edit', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createForm('tracker_issueBundle_issue', $entity, array(
                'action' => $this->generateUrl('issue_update', array('id' => $entity->getId())),
                'method' => 'PUT'
            ));

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     * @param integer $id
     * @param Request $request
     * @Route("/{id}", name="issue_update")
     * @Method("PUT")
     * @Template("TrackerIssueBundle:Default:edit.html.twig")
     * @return array
     */
    public function updateAction(Request $request, $id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TrackerIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $editForm = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('issue_update', array('id' => $entity->getId())),
                'method' => 'PUT'
            ));
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }


    /**
     * Edits an existing Issue entity.
     * @param Request $request
     * @param Issue $issue
     * @Route("/comment/{issue}", name="issue_comment_create")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("POST")
     * @Template("TrackerIssueBundle:Issue:edit.html.twig")
     * @return array
     */
    public function createCommentAction(Request $request, Issue $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Comment();
        $form = $this->createCreateCommentForm($entity, $issue->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreated(new \DateTime());
            $author = $this->get('security.context')->getToken()->getUser();
            $entity->setAuthor($author);
            $entity->setIssue($issue);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getIssue()->getId())));
        }

        return array(
            'entity' => $entity->getIssue(),
            'comment_form'   => $form->createView()
        );
    }

    /**
     * Deletes a Issue entity.
     * @param Request $request
     * @param Issue $issue
     * @param integer $id
     * @Route("/{issue}/comment/{comment}", name="issue_comment_delete")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @Method("GET")
     * @return array
     */
    public function deleteCommentAction(Request $request, $issue, $comment)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('delete', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $em->remove($comment);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('id'=>$issue->getId())));
    }

    /**
     * @param Comment $comment
     * @param integer $issueId
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateCommentForm(Comment $comment, $issueId)
    {

        $form = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
            'action' => $this->generateUrl('issue_comment_create', array('issue'=>$issueId)),
            'method' => 'POST'
        ));

        return $form;
    }


    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/{issue}/new", name="issue_new_sub_task")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("GET")
     * @Template()
     */
    public function newSubTaskAction (Request $request, $issue) {
        if (false === $this->get('security.authorization_checker')->isGranted(IssueVoter::CREATE_SUB_TASK, $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();

        $form  = $this->createForm('tracker_issueBundle_issueSubTask_form', $entity, array(
            'action' => $this->generateUrl('issue_create_sub_task', array('issue'=>$issue->getId())),
            'method' => 'POST',
            'issueStory' => $issue
        ));

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Issue entity.
     * @param Request $request
     * @Route("/{issue}", name="issue_create_sub_task")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("POST")
     * @Template("TrackerIssueBundle:Issue:new.html.twig")
     * @return array
     */
    public function createSubTaskAction(Request $request, $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(IssueVoter::CREATE_SUB_TASK, $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();
        $form  = $this->createForm('tracker_issueBundle_issueSubTask_form', $entity, array(
            'action' => $this->generateUrl('issue_create_sub_task', array('issue'=>$issue->getId())),
            'method' => 'POST',
            'issueStory' => $issue
        ));
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $repositoryStatus = $em->getRepository('TrackerIssueBundle:Status');
            $repositoryType = $em->getRepository('TrackerIssueBundle:Type');

            $entityStatus = $repositoryStatus->findByValue(Status::STATUS_OPEN)[0];
            $entityType = $repositoryType->findByValue(Type::TYPE_SUB_TASK)[0];
            $entity->setCreated(new \DateTime());
            $entity->setUpdated(new \DateTime());
            $entity->setParent($issue);
            $entity->setProject($issue->getProject());
            $entity->setType($entityType);
            $entity->setStatus($entityStatus);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }
}
