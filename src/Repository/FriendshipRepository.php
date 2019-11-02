<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /**
     * @return Users.id Returns an array of Users id
     */
    public function findFriendList(int $id)
    {
        return array_map("current", $this->createQueryBuilder('f')
            ->orWhere('f.ask = :val')
            ->orWhere('f.receive = :val')
            ->select('CASE WHEN (f.ask = :val) THEN IDENTITY(f.receive) ELSE IDENTITY(f.ask) END')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult());
    }

    /**
     * @return Friendship object
     */
    public function checkLinkUser(Int $idUserA, Int $idUserB)
    {
        return $this->createQueryBuilder('f')
            ->orwhere(
                new Expr\Andx([
                    "f.ask = :userAA",
                    "f.receive = :userAB",
                ])
            )
            ->orWhere(
                new Expr\Andx([
                    "f.ask = :userBA",
                    "f.receive = :userBB",
                ])
            )
            ->setParameter("userAA", $idUserA)
            ->setParameter("userAB", $idUserB)
            ->setParameter("userBA", $idUserB)
            ->setParameter("userBB", $idUserA)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /*
    public function findOneBySomeField($value): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
