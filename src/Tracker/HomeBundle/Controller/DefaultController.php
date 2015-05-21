<?php

namespace Tracker\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Tracker\ActivitiesBundle\Entity\ActivityRepository;
use Tracker\IssueBundle\Entity\IssueRepository;
use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\StatusRepository;
use Tracker\IssueBundle\Entity\Status;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home_page")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
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
            /**
             * @var $statusRepository StatusRepository
             */
            $statusRepository = $em->getRepository('TrackerIssueBundle:Status');
            $status = $statusRepository->findByValue(Status::STATUS_CLOSED);

            if ($user instanceof User) {
                $issues = $issueRepository->findAvailableForUser($user, $status);
                $activity = $activityRepository->findAvailableForUser($user);
            }

            return array(
                'issues' => $issues,
                'activities'=>$activity
            );
        }

        return $this->redirect($this->generateUrl('fos_user_security_login'));
    }
}
