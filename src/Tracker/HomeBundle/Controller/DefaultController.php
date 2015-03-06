<?php

namespace Tracker\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\IssueRepository;
use Tracker\ActivitiesBundle\Entity\ActivityRepository;

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

            /**
             * @var $issueRepository IssueRepository
             */
            $issueRepository = $em->getRepository('TrackerIssueBundle:Issue');
            /**
             * @var $activityRepository ActivityRepository
             */
            $activityRepository = $em->getRepository('TrackerActivitiesBundle:Activity');
            if ($user instanceof User) {
                $issues = $issueRepository->findByCollaborator($user);
                $activity = $activityRepository->findByUser($user);
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
