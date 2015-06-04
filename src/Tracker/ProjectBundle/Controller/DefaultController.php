<?php

namespace Tracker\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Tracker\ProjectBundle\Entity\Project;
use Tracker\ProjectBundle\Entity\ProjectRepository;
use Tracker\ProjectBundle\Form\ProjectType;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Status;
use Tracker\IssueBundle\Entity\Issue;

/**
 * Project controller.
 *
 * @Route("/project")
 */
class DefaultController extends Controller
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        /**
         * @var $projectRepository ProjectRepository
         */
        $projectRepository = $entityManager->getRepository('TrackerProjectBundle:Project');
        $entities = $projectRepository->findByCollaborator($user);

        return array(
            'entities' => $entities,
            'emptyEntity' => new Project()
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="project_create")
     * @Method("POST")
     * @Template("TrackerProjectBundle:Default:new.html.twig")
     */
    public function createAction(Request $request)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setCreated(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_show', array('project' => $entity->getCode())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Project();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @param $project Project
     *
     * @return array
     *
     * @Route("/{project}", name="project_show")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     */
    public function showAction($project)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.authorization_checker')->isGranted('view', $project)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByProject($project);

        return array(
            'entity' => $project,
            'activity' => $activity
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @param $project Project
     *
     * @return array
     *
     * @Route("/{project}/issues", name="project_issues")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     */
    public function issuesAction($project)
    {
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.authorization_checker')->isGranted('view', $project)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $issues = $em->getRepository('TrackerIssueBundle:Issue')->findByProject($project);
        return array(
            'entity' => $project,
            'entities' => $issues,
            'emptyEntity' => new Issue()
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @param $project Project
     *
     * @return array
     *
     * @Route("/{project}/issue/new", name="project_new_issue")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     */
    public function newIssueAction($project)
    {
        $entity = new Issue();

        if (false === $this->get('security.authorization_checker')->isGranted('create', $entity)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('project_create_issue', ['project' => $project->getCode()]),
            'method' => 'POST',
            'projects' => [$project],
            'selectedProject' => $project
        ));

        return array(
            'project' => $project,
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Issue entity.
     *
     * @param Request $request
     * @param $project Project
     *
     * @Route("/{project}/issue/new", name="project_create_issue")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("POST")
     * @Template("TrackerProjectBundle:Default:newIssue.html.twig")
     * @return array
     */
    public function createIssueAction(Request $request, $project)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new Issue())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new Issue();
        /**
         * @var $user User
         */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $projects = array();
        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_MANAGER)) {
            $projects = $em->getRepository('TrackerProjectBundle:Project')->findAll();
        } else {
            $projects = $user->getProject();
        }

        $form = $this->createForm('tracker_issueBundle_issue', $entity, array(
            'action' => $this->generateUrl('project_new_issue', ['project' => $project->getCode()]),
            'method' => 'POST',
            'projects' => $projects,
            'selectedProject' => $project
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

            return $this->redirect($this->generateUrl('project_issues', array('project' => $project->getCode())));
        }

        return array(
            'entity' => $entity,
            'project' => $project,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @param $project Project
     *
     * @return array
     *
     * @Route("/{project}/edit", name="project_edit")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("GET")
     * @Template()
     */
    public function editAction($project)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $project)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($project);

        return array(
            'entity' => $project,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return Form The form
     */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_update', array('project' => $entity->getCode())),
            'method' => 'PUT'
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @param $project Project,
     * @param Request $request
     *
     * @return array
     *
     * @Route("/{project}", name="project_update")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "findOneByCode"})
     * @Method("PUT")
     * @Template("TrackerProjectBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $project)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $project)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $deletedMembers = $this->getDeletedMembers($project, $request);

        $editForm = $this->createEditForm($project);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityManager->flush();

            if (count($deletedMembers)) {
                $this->addFlash(
                    'notice',
                    'You have deleted users ' . implode(',', $deletedMembers) . ' that are assigned to issues'
                );
            }

            return $this->redirect($this->generateUrl('project_show', array('project' => $project->getCode())));
        }

        return array(
            'entity' => $project,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * @param Project $project
     * @param $request
     * @return array
     */
    protected function getDeletedMembers(Project $project, $request)
    {
        $members = $project->getMembers();
        $newMembers = $request->request->get('tracker_projectBundle_project')['members'];

        $deletedMembers = [];
        /**
         * @var User $member
         */
        foreach ($members as $member) {
            if (!in_array((string)$member->getid(), $newMembers, true) &&
                $member->getAssignedIssue()->count() > 0
            ) {
                $deletedMembers[] = $member->getUsername();
            }
        }

        return $deletedMembers;
    }
}
