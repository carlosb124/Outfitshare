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

    public function findBySearchAndCategory($search = null, $category = null)
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
                    // For other categories, we search them as keywords in title/desc
                    $qb->andWhere('o.titulo LIKE :category OR o.descripcion LIKE :category')
                        ->setParameter('category', '%' . $category . '%');
                    break;
            }
        }

        return $qb->getQuery()->getResult();
    }
}