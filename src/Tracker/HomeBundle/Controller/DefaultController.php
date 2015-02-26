<?php

namespace Tracker\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\UserBundle\Entity\User;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();

            $issues = array();
            $activity = array();
            if ($user instanceof User) {
                $issues = $em->getRepository('TrackerIssueBundle:Issue')->findByCollaborator($user);
                $activity = $em->getRepository('TrackerActivitiesBundle:Activity')->findByUser($user);
            }

            return array(
                'issues' => $issues,
                'activities'=>$activity
            );
        } else {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }
}
