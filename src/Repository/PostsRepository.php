<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Posts>
 */
class PostsRepository extends ServiceEntityRepository {

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Posts::class);
  }

  // /**
  //     * @return Posts[] Returns an array of Posts objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('p')
  //            ->andWhere('p.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('p.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }
  // public function findOneBySomeField($value): ?Posts
  //    {
  //        return $this->createQueryBuilder('p')
  //            ->andWhere('p.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
  public function search(string $query): array {
    return $this->createQueryBuilder('p')
      ->leftJoin('p.userID', 'u')
      ->leftJoin('p.cat', 'c')
      ->where('p.contenu LIKE :query')
      ->orWhere('u.username LIKE :query')
      ->orWhere('c.name LIKE :query')
      ->setParameter('query', '%' . $query . '%')
      ->orderBy('p.date', 'DESC')
      ->getQuery()
      ->getResult();
  }

  public function findMostCommentedPosts(int $limit = 3): array {
    return $this->createQueryBuilder('p')
      ->select('p', 'COUNT(c.id) as commentCount')
      ->leftJoin('p.commentaires', 'c')
      ->groupBy('p.id')
      ->orderBy('commentCount', 'DESC')
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();
  }

  public function findHotPosts(User $user, int $page = 1, int $limit = 5): array {
    // Récupérer les catégories suivies par l'utilisateur.
    $followedCategories = $user->getAbonnements()
      ->filter(fn($abonnement) => $abonnement->getCategory() !== NULL)
      ->map(fn($abonnement) => $abonnement->getCategory())
      ->toArray();

    // Récupérer les posts des catégories suivies et les posts tendance.
    return $this->createQueryBuilder('p')
      ->select('DISTINCT p')
      ->leftJoin('p.commentaires', 'c')
      ->where('p.cat IN (:categories)')
      ->orWhere('p.id IN (
                SELECT p2.id 
                FROM App\Entity\Posts p2 
                LEFT JOIN p2.commentaires c2 
                GROUP BY p2.id 
                ORDER BY COUNT(c2.id) DESC
            )')
      ->setParameter('categories', $followedCategories)
      ->orderBy('p.date', 'DESC')
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();
  }

  public function findNewPosts(User $user, int $page = 1, int $limit = 5): array {
    // Récupérer les catégories suivies par l'utilisateur.
    $followedCategories = $user->getAbonnements()
      ->filter(fn($abonnement) => $abonnement->getCategory() !== NULL)
      ->map(fn($abonnement) => $abonnement->getCategory())
      ->toArray();

    return $this->createQueryBuilder('p')
      ->where('p.cat IN (:categories)')
      ->setParameter('categories', $followedCategories)
      ->orderBy('p.date', 'DESC')
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();
  }

  public function findFriendsPosts(User $user, int $page = 1, int $limit = 5): array {
    // Récupérer les utilisateurs suivis.
    $followedUsers = $user->getAbonnements()
      ->filter(fn($abonnement) => $abonnement->getFollowedUser() !== NULL)
      ->map(fn($abonnement) => $abonnement->getFollowedUser())
      ->toArray();

    return $this->createQueryBuilder('p')
      ->where('p.userID IN (:users)')
      ->setParameter('users', $followedUsers)
      ->orderBy('p.date', 'DESC')
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery()
      ->getResult();
  }

  public function countPostsByFilter(User $user, string $filter): int {
    $qb = $this->createQueryBuilder('p')
      ->select('COUNT(DISTINCT p.id)');

    switch ($filter) {
      case 'hot':
        $followedCategories = $user->getAbonnements()
          ->filter(fn($abonnement) => $abonnement->getCategory() !== NULL)
          ->map(fn($abonnement) => $abonnement->getCategory())
          ->toArray();
        $qb->leftJoin('p.commentaires', 'c')
          ->where('p.cat IN (:categories)')
          ->orWhere('p.id IN (
                       SELECT p2.id 
                       FROM App\Entity\Posts p2 
                       LEFT JOIN p2.commentaires c2 
                       GROUP BY p2.id 
                       ORDER BY COUNT(c2.id) DESC
                   )')
          ->setParameter('categories', $followedCategories);
        break;

      case 'new':
        $followedCategories = $user->getAbonnements()
          ->filter(fn($abonnement) => $abonnement->getCategory() !== NULL)
          ->map(fn($abonnement) => $abonnement->getCategory())
          ->toArray();
        $qb->where('p.cat IN (:categories)')
          ->setParameter('categories', $followedCategories);
        break;

      case 'friends':
        $followedUsers = $user->getAbonnements()
          ->filter(fn($abonnement) => $abonnement->getFollowedUser() !== NULL)
          ->map(fn($abonnement) => $abonnement->getFollowedUser())
          ->toArray();
        $qb->where('p.userID IN (:users)')
          ->setParameter('users', $followedUsers);
        break;
    }

    return (int) $qb->getQuery()->getSingleScalarResult();
  }

  public function findOneByIdWithCommentsAndReplies(int $id): ?Posts {
    return $this->createQueryBuilder('p')
      ->addSelect('c', 'r')
      ->leftJoin('p.commentaires', 'c')
      ->leftJoin('c.commentaires', 'r')
      ->where('p.id = :id')
      ->setParameter('id', $id)
    // Order top-level comments.
      ->orderBy('c.creation_date', 'ASC')
    // Order replies.
      ->addOrderBy('r.creation_date', 'ASC')
      ->getQuery()
      ->getOneOrNullResult();
  }

}
