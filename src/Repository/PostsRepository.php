<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Posts;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * @return Posts[] Returns an array of Posts objects
     */
    public function getFriendsPosts(User $user, $nbr)
    {
        return $this->createQueryBuilder('p')
            ->orwhere(
                new Expr\Orx([
                    "p.author IN (:friend)",
                    "p.user IN (:user)",
                ])
            )
            ->orwhere(
                new Expr\Orx([
                    "p.user IN (:friend)",
                    "p.author IN (:user)",
                ])
            )
            ->setParameter('friend', $user->getFriends())
            ->setParameter('user', $user->getId())
            ->orderBy('p.date', 'DESC')
            ->setFirstResult($nbr)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Posts[] Returns an array of Posts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
