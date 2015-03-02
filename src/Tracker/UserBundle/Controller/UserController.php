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
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
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
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
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
//        $deleteForm = $this->createDeleteForm($userId);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Project entity.
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
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Project entity.
     * @param integer $userId,
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     *
     * @Route("/{userId}", name="user_update")
     * @Method("PUT")
     * @Template("TrackerUserBundle:Default:edit.html.twig")
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

//        $deleteForm = $this->createDeleteForm($projectId);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $entityManager->flush();

            return $this->redirect($this->generateUrl('user_show', array('userId' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        );
    }
}
