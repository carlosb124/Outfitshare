<?php

namespace App\Repository;

use App\Entity\FollowRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FollowRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FollowRequest::class);
    }

    /**
     * Find a follow request between two specific users.
     */
    public function findBetweenUsers(User $requester, User $target): ?FollowRequest
    {
        return $this->findOneBy([
            'requester' => $requester,
            'target' => $target,
        ]);
    }

    /**
     * Find all pending follow requests received by a user.
     */
    public function findPendingForUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->leftJoin('fr.requester', 'r')
            ->addSelect('r')
            ->where('fr.target = :user')
            ->andWhere('fr.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FollowRequest::STATUS_PENDING)
            ->orderBy('fr.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}