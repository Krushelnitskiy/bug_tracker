<?php

namespace Tracker\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        $entityManager = $this->getDoctrine()->getManager();
        $entities = $entityManager->getRepository('TrackerProjectBundle:Project')->findAll();

        return array(
            'entities' => $entities,
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
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);



        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_show', array('projectId' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
            'method' => 'POST',
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
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     * @param $projectId integer
     * @return array
     *
     * @Route("/{projectId}", name="project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($projectId)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('view', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerProjectBundle:Project')->find($projectId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($projectId);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     * @param $projectId
     * @return array
     *
     * @Route("/{projectId}/edit", name="project_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($projectId)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entity = $entityManager->getRepository('TrackerProjectBundle:Project')->find($projectId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($projectId);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Project $entity)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_update', array('projectId' => $entity->getId())),
            'method' => 'PUT',
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
     * @Route("/{projectId}", name="project_update")
     * @Method("PUT")
     * @Template("TrackerProjectBundle:Default:edit.html.twig")
     */
    public function updateAction(Request $request, $projectId)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new Project())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('TrackerProjectBundle:Project')->find($projectId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($projectId);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $entityManager->flush();

            return $this->redirect($this->generateUrl('project_show', array('projectId' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Project entity.
     * @param integer $projectId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{projectId}", name="project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $projectId)
    {
        $form = $this->createDeleteForm($projectId);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entity = $entityManager->getRepository('TrackerProjectBundle:Project')->find($projectId);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $entityManager->remove($entity);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param mixed $projectId The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($projectId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('projectId' => $projectId)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }
}
