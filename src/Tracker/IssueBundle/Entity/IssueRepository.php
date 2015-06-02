<?php

namespace Tracker\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Tracker\UserBundle\Entity\User;

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
            ->where('c.id = :user_id')
            ->setParameter('user_id', $user->getId());

        if (is_array($skipStatus) and count($skipStatus)>0) {
            $query->andWhere('issue.status not in (:skipStatus)')
                ->setParameter('skipStatus', $skipStatus);
        }

        return $query->getQuery()->getResult();
    }
}
