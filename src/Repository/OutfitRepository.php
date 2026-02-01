<?php

namespace App\Repository;

use App\Entity\Outfit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutfitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outfit::class);
    }

    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->orderBy('o.fechaPublicacion', 'DESC');

        if ($search) {
            $qb->andWhere('o.titulo LIKE :search OR o.descripcion LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            switch ($category) {
                case 'Trending':
                    $qb->leftJoin('o.likes', 'l')
                       ->groupBy('o.id')
                       ->orderBy('COUNT(l.id)', 'DESC');
                    break;
                case 'Accessories':
                    // JSON field check not always standard in pure DQL, but we can check if it's not empty textually 
                    // or just if description/title contains 'accessory' as a fallback?
                    // Actually, valid JSON array for empty is '[]'. 
                    // Let's assume non-empty accessories means searching for key words or checking field length > 2
                     $qb->andWhere("o.accessories != '[]' AND o.accessories IS NOT NULL");
                    break;
                case 'For You':
                    // Default logic (newest)
                    break;
                default:
                    // For other categories, we search them as keywords in title/desc
                    $qb->andWhere('o.titulo LIKE :category OR o.descripcion LIKE :category')
                       ->setParameter('category', '%' . $category . '%');
                    break;
            }
        }

        return $qb->getQuery()->getResult();
    }
}
