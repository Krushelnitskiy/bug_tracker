<?php

namespace Tracker\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\UserBundle\Entity\User;
use Tracker\UserBundle\Form\UserType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

        if (false === $this->get('security.authorization_checker')->isGranted('view', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TrackerUserBundle:User')->findAll();

        return array(
            'entities' => $entities
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('create', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a User entity.
     * @param integer $id
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function showAction($id)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('view', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TrackerUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     * @param $userId
     * @return array
     *
     * @Route("/{userId}/edit", name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($userId)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('TrackerUserBundle:User')->find($userId);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('userId' => $entity->getId())),
            'method' => 'PUT'
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     * @param integer $userId,
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     *
     * @Route("/{userId}", name="user_update")
     * @Method("PUT")
     * @Template("TrackerUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $userId)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', new User())) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('TrackerUserBundle:User')->find($userId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityManager->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(user $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a new User entity.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("TrackeruserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);



        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
}
