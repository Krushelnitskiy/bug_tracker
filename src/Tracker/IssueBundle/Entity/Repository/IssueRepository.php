<?php

namespace Tracker\IssueBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Tracker\UserBundle\Entity\User;
use Tracker\IssueBundle\Entity\Status;

class IssueRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function findByCollaborator(User $user)
    {
        $qb = $this->createQueryBuilder('issue');

        $query = $qb
            ->leftJoin('issue.collaborators', 'collaborators')
            ->where($qb->expr()->eq('collaborators.id', $user->getId()))
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     * @param Status[] $skipStatus
     *
     * @return array
     */
    public function findAvailableForUser(User $user, $skipStatus)
    {
        $query = $this->createQueryBuilder('issue')
            ->leftJoin('issue.collaborators', 'c')
            ->leftJoin('issue.project', 'p')
            ->leftJoin('p.members', 'm')
            ->where('c.id = :user_id')
            ->andWhere('m.id = :user_id')
            ->setParameter('user_id', $user->getId())
            ->setMaxResults(10);

        if (is_array($skipStatus) and count($skipStatus)>0) {
            $query->andWhere('issue.status not in (:skipStatus)')
                ->setParameter('skipStatus', $skipStatus);
        }

        return $query->getQuery()->getResult();
    }

    public function findByUser(User $user)
    {
        $query = $this->createQueryBuilder('issue')
            ->leftJoin('issue.project', 'p')
            ->leftJoin('p.members', 'm')
            ->where('m.id = :user_id')
            ->andWhere('issue.assignee = :user_id')
            ->setParameter('user_id', $user->getId());

        return $query->getQuery()->getResult();
    }
}
