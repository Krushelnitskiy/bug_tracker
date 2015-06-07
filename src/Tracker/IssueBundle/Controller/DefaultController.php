<?php

namespace Tracker\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
 */
class DefaultController extends Controller
{
    /**
     * Lists all Issue entities.

     * @param Project $project
     *
     * @Route("/issues", name="issue")
     * @Route("/project/{project}/issues", name="project_issues")
     *
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Project $project = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        if ($project instanceof Project) {
            $entities = $em->getRepository('TrackerIssueBundle:Issue')->findByProject($project);
        } else {
            $entities = $em->getRepository('TrackerIssueBundle:Issue')->findByUser($user);
        }

        return array(
            'project' => $project,
            'entities' => $entities,
            'emptyEntity' => new Issue()
        );
    }

    /**
     * Lists all Issue entities.
     *
     * @param Request $request
     *
     * @Route("/issue/form", name="issue_form_data")
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
                    'username' => $member->getUsername(),
                    'id' => $member->getId()
                ];
            }
        }

        return new JsonResponse(array(
            'members' => $members
        ));
    }

    /**
     * Creates a new Issue entity.
     *
     * @param Request $request
     * @param Project $project
     *
     * @Route("/issue/new", name="issue_new")
     * @Route("project/{project}/issue/new", name="project_new_issue")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     *
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:new.html.twig")
     *
     * @return array
     */
    public function createAction(Request $request, Project $project = null)
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

        if ($project instanceof Project) {
            $form = $this->createForm('tracker_issueBundle_issue', $entity, array(
                'action' => $this->generateUrl('project_new_issue', ['project' => $project->getCode()]),
                'method' => 'POST',
                'projects' => [$project],
                'selectedProject' => $project
            ));
        } else {
            if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
                $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
            } else {
                $projects = $user->getProject();
            }

            $form = $this->createForm('tracker_issueBundle_issue', $entity, array(
                'action' => $this->generateUrl('issue_new'),
                'method' => 'POST',
                'projects' => $projects
            ));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreated(new \DateTime());
            $entity->setUpdated(new \DateTime());

            $entityStatus = $em->getRepository('TrackerIssueBundle:Status')->findOneByValue(Status::STATUS_OPEN);
            $entity->setStatus($entityStatus);

            $em->persist($entity);
            $em->flush();

            if ($project instanceof Project) {
                return $this->redirect($this->generateUrl('project_issues', array('project' => $project->getCode())));
            }

            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getCode())));
        }

        return array(
            'project' => $project,
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @param Issue $issue
     * @param Project $project
     *
     * @Route("/issue/{issue}", name="issue_show")>
     * @Route("/project/{project}/issue/{issue}", name="project_issue_show")>
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function showAction(Issue $issue = null, Project $project = null)
    {
        if ($issue === true ||
            false === $this->get('security.authorization_checker')->isGranted('view', $issue)
        ) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $createCommentForm = $this->createCreateCommentForm(new Comment(), $issue->getCode(), $project);
        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByIssue($issue);

        return array(
            'project' => $project,
            'entity' => $issue,
            'activity' => $activity,
            'comment_form' => $createCommentForm->createView()
        );
    }

    /**
     * @param Comment $comment
     * @param integer $issueId
     * @param Project $project
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateCommentForm(Comment $comment, $issueId, $project)
    {
        $route = $this->generateUrl('issue_comment_create', array('issue' => $issueId));

        if ($project instanceof Project) {
            $routeParam = array('issue' => $issueId, 'project'=>$project->getCode());
            $route = $this->generateUrl('project_issue_comment_create', $routeParam);
        }

        $form = $this->createForm('tracker_issueBundle_comment_form', $comment, array(
            'action' => $route,
            'method' => 'POST'
        ));

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @param Issue $issue
     * @param Request $request
     * @param Project $project
     *
     * @Route("/issue/{issue}/edit", name="issue_edit")
     * @Route("/project/{project}/issue/{issue}/edit", name="project_issue_edit")
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:edit.html.twig")
     *
     * @return array
     */
    public function editAction(Request $request, $issue, Project $project = null)
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

        if ($project instanceof Project) {
            $routeParams = ['issue' => $issue->getCode(), 'project' => $project->getCode()];
            $editForm = $this->createForm('tracker_issueBundle_issue', $issue, array(
                'action' => $this->generateUrl('project_issue_edit', $routeParams),
                'method' => 'POST',
                'projects' => $projects
            ));
        } else {
            $editForm = $this->createForm('tracker_issueBundle_issue', $issue, array(
                'action' => $this->generateUrl('issue_edit', array('issue' => $issue->getCode())),
                'method' => 'POST',
                'projects' => $projects
            ));
        }

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            if ($project instanceof Project) {
                $routeParams = array('project' => $project->getCode(), 'issue' => $issue->getCode());
                return $this->redirect($this->generateUrl('project_issue_show', $routeParams));
            }
            return $this->redirect($this->generateUrl('issue_show', array('issue' => $issue->getCode())));
        }

        return array(
            'project' => $project,
            'entity' => $issue,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Creates a new Issue entity.
     *
     * @param Request $request
     * @param Issue $issue
     * @param Project $project
     *
     * @Route("/issue/{issue}/new", name="issue_new_sub_task")
     * @Route("/project/{project}/issue/{issue}/new", name="project_issue_new_sub_task")
     *
     * @ParamConverter("issue", class="TrackerIssueBundle:Issue", options={"repository_method" = "findOneByCode"})
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method({"GET", "POST"})
     * @Template("TrackerIssueBundle:Default:newSubTask.html.twig")
     *
     * @return array
     */
    public function createSubTaskAction(Request $request, $issue, Project $project = null)
    {
        if (false === $this->get('security.authorization_checker')->isGranted(IssueVoter::CREATE_SUB_TASK, $issue)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $projects = [$issue->getProject()];

        $entity = new Issue();
        $form = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('issue_new_sub_task', array('issue' => $issue->getCode())),
            'method' => 'POST',
            'projects' => $projects,
            'issueStory' => $issue,
            'typeIssue' => Type::TYPE_SUB_TASK
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

            if ($project instanceof Project) {
                $routeParams = array('project' => $project->getCode(), 'issue' => $entity->getCode());
                return $this->redirect($this->generateUrl('project_issue_show', $routeParams));
            }
            return $this->redirect($this->generateUrl('issue_show', array('issue' => $entity->getCode())));
        }

        return array(
            'project' => $project,
            'parent'=>$issue,
            'entity' => $entity,
            'form' => $form->createView()
        );
    }
}
