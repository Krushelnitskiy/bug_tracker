<?php

namespace Tracker\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

use Tracker\UserBundle\Entity\User;

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
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Finds and displays a User entity.
     * @param User $user
     * @Route("/{user}", name="user_show")
     * @ParamConverter("user", class="TrackerUserBundle:User", options={"repository_method" = "find"})
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function showAction($user)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('view', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getManager();
        $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByUser($user);

        return array(
            'entity'      => $user,
            'activity'      => $activity
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     * @param User $user
     * @return array
     * @ParamConverter("user", class="TrackerUserBundle:User", options={"repository_method" = "find"})
     * @Route("/{user}/edit", name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($user)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $editForm = $this->createEditForm($user);

        return array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a User entity.
     * @param User $entity The entity
     * @return Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm('tracker_userBundle_user', $entity, array(
            'action' => $this->generateUrl('user_update', array('user' => $entity->getId())),
            'method' => 'PUT'
        ));

        return $form;
    }

    /**
     * Edits an existing User entity.
     * @param User $user,
     * @param Request $request
     *
     * @return array
     *
     * @Route("/{user}", name="user_update")
     * @ParamConverter("user", class="TrackerUserBundle:User", options={"repository_method" = "find"})
     * @Method("PUT")
     * @Template("TrackerUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $user)
    {
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $user)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entityManager->flush();

            return $this->redirect($this->generateUrl('user_show', array('user' => $user->getId())));
        }

        return array(
            'entity'      => $user,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(user $entity)
    {
        $form = $this->createForm('tracker_userBundle_user', $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'PUT'
        ));

        return $form;
    }

    /**
     * Creates a new User entity.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     *
     * @Route("/", name="user_create")
     * @Method("PUT")
     * @Template("TrackerUserBundle:User:new.html.twig")
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

            return $this->redirect($this->generateUrl('user_show', array('user' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }
}
