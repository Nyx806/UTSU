<?php

namespace App\Repository;

use App\Entity\Abonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnement>
 */
class AbonnementRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

  // /**
  //     * @return Abonnement[] Returns an array of Abonnement objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('a')
  //            ->andWhere('a.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('a.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }
  // public function findOneBySomeField($value): ?Abonnement
  //    {
  //        return $this->createQueryBuilder('a')
  //            ->andWhere('a.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
