<?php

namespace Tracker\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
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
use Tracker\ProjectBundle\Entity\Project;

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
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entities = $em->getRepository('TrackerIssueBundle:Issue')->findByUser($user);

        return array(
            'entities' => $entities,
            'emptyEntity' => new Issue()
        );
    }

    /**
     * Lists all Issue entities.
     *
     * @param Request $request
     *
     * @Route("/form", name="issue_form_data")
     * @Method("POST")
     *
     * @return JsonResponse
     */
    public function formAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $projectId = $request->get('projectId');
        $em = $this->getDoctrine()->getManager();
        /**
         * @var $project Project
         */
        $project = $em->getRepository('TrackerProjectBundle:Project')->find($projectId);
        $members = [];

        if (false !== $this->get('security.authorization_checker')->isGranted('view', $project)) {
            /**
             * @var $member User
             */
            foreach ($project->getMembers() as $member) {
                $members[] = [
                    'fullName' => $member->getFullName(),
                    'id' => $member->getId()
                ];
            }
        }

        return new JsonResponse(array(
            'members'   => $members
        ));
    }

    /**
     * Creates a new Issue entity.
     *
     * @param Request $request
     *
     * @Route("/new", name="issue_new")
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:new.html.twig")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $entity = new Issue();
        if (false === $this->get('security.authorization_checker')->isGranted('create', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        /**
         * @var $user User
         */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
            $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
        } else {
            $projects = $user->getProject();
        }

        $form  = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('issue_new'),
            'method' => 'POST',
            'projects' => $projects
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreated(new \DateTime());
            $entity->setUpdated(new \DateTime());

            $entityStatus = $em->getRepository('TrackerIssueBundle:Status')->findOneByValue(Status::STATUS_OPEN);
            $entity->setStatus($entityStatus);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getCode())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @param $issue Issue
     *
     * @Route("/{issue}", name="issue_show")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function showAction(Issue $issue = null)
    {
        if ($issue === true ||
            false === $this->get('security.authorization_checker')->isGranted('view', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $createCommentForm = $this->createCreateCommentForm(new Comment(), $issue->getCode());
        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByIssue($issue);

        return array(
            'entity' => $issue,
            'activity' => $activity,
            'comment_form' => $createCommentForm->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     *
     * @param Issue $issue
     * @param Request $request
     *
     * @Route("/{issue}/edit", name="issue_edit")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:edit.html.twig")
     *
     * @return array
     */
    public function editAction(Request $request, $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
            $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
        } else {
            $projects = $user->getProject();
        }

        $editForm = $this->createForm('tracker_issueBundle_issue', $issue, array(
            'action' => $this->generateUrl('issue_edit', array('issue' => $issue->getCode())),
                'method' => 'POST',
                'projects' => $projects
            ));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $issue->getCode())));
        }

        return array(
            'entity'      => $issue,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Edits an existing Issue entity.
     *
     * @param Request $request
     * @param Issue $issue
     *
     * @Route("/comment/{issue}", name="issue_comment_create")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @Method("POST")
     * @Template("TrackerIssueBundle:Issue:edit.html.twig")
     *
     * @return array
     */
    public function createCommentAction(Request $request, Issue $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Comment();
        $form = $this->createCreateCommentForm($entity, $issue->getCode());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setCreated(new \DateTime());
            $author = $this->getUser();
            $entity->setAuthor($author);
            $entity->setIssue($issue);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getIssue()->getCode())));
        }

        return array(
            'entity' => $entity->getIssue(),
            'comment_form'   => $form->createView()
        );
    }

    /**
     * Deletes a Issue entity.
     *
     * @param Issue $issue
     * @param Comment $comment
     *
     * @Route("/{issue}/comment/{comment}", name="issue_comment_delete")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("comment", class="TrackerIssueBundle:Comment", options={"repository_method" = "find"})
     * @Method("GET")
     *
     * @return array
     */
    public function deleteCommentAction($issue, $comment)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('delete', $comment)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('issue'=>$issue->getCode())));
    }

    /**
     * @param Comment $comment
     * @param integer $issueId
     *
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
     * Creates a new Issue entity.
     *
     * @param Request $request
     * @param Issue $issue
     *
     * @Route("/{issue}/new", name="issue_new_sub_task")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:newSubTask.html.twig")
     *
     * @return array
     */
    public function createSubTaskAction(Request $request, $issue)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(IssueVoter::CREATE_SUB_TASK, $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();
        $form  = $this->createForm('tracker_issueBundle_issueSubTask_form', $entity, array(
            'action' => $this->generateUrl('issue_new_sub_task', array('issue'=>$issue->getCode())),
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

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getCode())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }
}
