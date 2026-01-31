<?php

namespace App\Repository;

use App\Entity\SavedOutfit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SavedOutfit>
 *
 * @method SavedOutfit|null find($id, $lockMode = null, $lockVersion = null)
 * @method SavedOutfit|null findOneBy(array $criteria, array $orderBy = null)
 * @method SavedOutfit[]    findAll()
 * @method SavedOutfit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavedOutfitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavedOutfit::class);
    }

    //    /**
//     * @return SavedOutfit[] Returns an array of SavedOutfit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?SavedOutfit
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
