<?php

namespace App\Repository;

use App\Entity\Outfit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutfitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outfit::class);
    }

    public function findBySearchAndCategory($search = null, $category = null, ?User $currentUser = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->orderBy('o.fechaPublicacion', 'DESC');

        // Privacy filter: only show outfits from public profiles,
        // users the current user follows, or own outfits
        if ($currentUser) {
            // Get IDs of users the current user follows
            $followingIds = [];
            foreach ($currentUser->getFollowing() as $followedUser) {
                $followingIds[] = $followedUser->getId();
            }

            if (!empty($followingIds)) {
                $qb->andWhere('u.isPublic = true OR u.id = :currentUserId OR u.id IN (:followingIds)')
                    ->setParameter('currentUserId', $currentUser->getId())
                    ->setParameter('followingIds', $followingIds);
            }
            else {
                $qb->andWhere('u.isPublic = true OR u.id = :currentUserId')
                    ->setParameter('currentUserId', $currentUser->getId());
            }
        }
        else {
            // No authenticated user: only show public
            $qb->andWhere('u.isPublic = true');
        }

        if ($search) {
            $qb->andWhere('o.titulo LIKE :search OR o.descripcion LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            switch ($category) {
                case 'Tendencias':
                    $qb->leftJoin('o.likes', 'l')
                        ->groupBy('o.id')
                        ->orderBy('COUNT(l.id)', 'DESC');
                    break;
                case 'Accesorios':
                    $qb->andWhere("o.accessories != '[]' AND o.accessories IS NOT NULL");
                    break;
                case 'Para Ti':
                    // Default logic (newest)
                    break;
                default:
                    $qb->andWhere('o.titulo LIKE :category OR o.descripcion LIKE :category')
                        ->setParameter('category', '%' . $category . '%');
                    break;
            }
        }

        return $qb->getQuery()->getResult();
    }
}