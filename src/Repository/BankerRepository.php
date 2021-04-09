<?php

namespace App\Repository;

use App\Entity\Banker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Banker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banker[]    findAll()
 * @method Banker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banker::class);
    }

    // /**
    //  * @return Banker[] Returns an array of Banker objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Banker
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
