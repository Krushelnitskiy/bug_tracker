<?php

namespace Tracker\IssueBundle\Controller;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Tracker\IssueBundle\Entity\Type;
use Tracker\IssueBundle\Entity\Comment;
use Tracker\IssueBundle\Entity\Issue;
use Tracker\IssueBundle\Entity\Status;
use Tracker\IssueBundle\Security\Authorization\Voter\IssueVoter;
use Tracker\UserBundle\Entity\User;

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

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getId())));
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

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a Issue entity.
     * @param $issue Issue
     * @Route("/{issue}", name="issue_show")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function showAction($issue)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.authorization_checker')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $createCommentForm = $this->createCreateCommentForm(new Comment(), $issue->getId());
        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByIssue($issue);

        return array(
            'entity'      => $issue,
            'activity'      => $activity,
            'comment_form' => $createCommentForm->createView()
        );
    }

    /**
     * Displays a form to edit an existing Issue entity.
     * @param Issue $issue
     * @Route("/{issue}/edit", name="issue_edit")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function editAction($issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createForm('tracker_issueBundle_issue', $issue, array(
                'action' => $this->generateUrl('issue_update', array('issue' => $issue->getId())),
                'method' => 'PUT'
            ));

        return array(
            'entity'      => $issue,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     * @param Issue $issue
     * @param Request $request
     * @Route("/{issue}", name="issue_update")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("PUT")
     * @Template("TrackerIssueBundle:Default:edit.html.twig")
     * @return array
     */
    public function updateAction(Request $request, $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('tracker_issueBundle_issue', $issue, array(
            'action' => $this->generateUrl('issue_update', array('issue' => $issue->getId())),
                'method' => 'PUT'
            ));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $issue->getId())));
        }

        return array(
            'entity'      => $issue,
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
        if (false === $this->get('security.authorization_checker')->isGranted('create', $issue)) {
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

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getIssue()->getId())));
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
     * @param Comment $comment
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
        $em->remove($comment);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('issue'=>$issue->getId())));
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
     * @param Request $request
     * @param Issue $issue
     *
     * @Route("/{issue}/new", name="issue_new_sub_task")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "find"})
     * @Method("GET")
     * @Template()
     */
    public function newSubTaskAction(Request $request, $issue)
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

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Issue entity.
     * @param Request $request
     * @param Issue $issue
     *
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

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }
}
