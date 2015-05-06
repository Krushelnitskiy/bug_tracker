<?php

namespace Tracker\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Tracker\UserBundle\Entity\User;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository
{
    public function findByCollaborator(User $user)
    {
        $qb = $this->createQueryBuilder('issue');

        $query = $qb
            ->leftJoin('issue.collaborators', 'collaborators')
            ->where($qb->expr()->eq('collaborators.id', $user->getId()))
            ->getQuery();

        return $query->getResult();
    }
}
