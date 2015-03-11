<?php

namespace Tracker\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Tracker\ProjectBundle\Entity\Project;
use Tracker\ProjectBundle\Form\ProjectType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        if (false === $this->get('security.authorization_checker')->isGranted('view', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entities = $entityManager->getRepository('TrackerProjectBundle:Project')->findAll();

        return array (
            'entities' => $entities,
            'emptyEntity' => new Project()
        );
    }

    /**
     * Creates a new Project entity.
     * @param \Symfony\Component\HttpFoundation\Request $request
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_show', array('project' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
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
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a Project entity.
     * @param $projectId integer
     * @return array
     *
     * @Route("/{project}", name="project_show")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "find"})
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
            'entity'      => $project,
            'activity'      => $activity
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     * @param $projectId
     * @return array
     *
     * @Route("/{project}/edit", name="project_edit")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "find"})
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
            'entity'      => $project,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a Project entity.
     * @param Project $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_update', array('project' => $entity->getId())),
            'method' => 'PUT'
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Project entity.
     * @param integer $projectId,
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     *
     * @Route("/{project}", name="project_update")
     * @ParamConverter("project", class="TrackerProjectBundle:Project", options={"repository_method" = "find"})
     * @Method("PUT")
     * @Template("TrackerProjectBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $project)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (false === $this->get('security.authorization_checker')->isGranted('edit', $project)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($project);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_show', array('project' => $project->getId())));
        }

        return array(
            'entity'      => $project,
            'edit_form'   => $editForm->createView()
        );
    }
}
