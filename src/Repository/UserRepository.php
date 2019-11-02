<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function findBySearchUsername(User $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username LIKE :val')
            ->setParameter('val', "%" . $user->getUsername() . "%")
            ->leftJoin('u.friends', 'friend')
            ->addSelect('friend')
            ->addOrderBy('u.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithLimit()
    {
        return $this->createQueryBuilder('u')
            ->addOrderBy('u.id', 'ASC');
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
