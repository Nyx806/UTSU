<?php

namespace App\Repository;

use App\Entity\Commentaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentaires>
 */
class CommentairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentaires::class);
    }

    //    /**
    //     * @return Commentaires[] Returns an array of Commentaires objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commentaires
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.userID', 'u')
            ->leftJoin('c.post', 'p')
            ->where('c.contenu LIKE :query')
            ->orWhere('u.username LIKE :query')
            ->orWhere('p.contenu LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.creation_date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
