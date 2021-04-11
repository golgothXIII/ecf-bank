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

    /**
     * @return Banker|null Returns the banker with the least account
     */
    public function findBankerWithLeastAccount(): ?Banker
    {
        //case where a banker does not yet have an account
        $bankers = $this->createQueryBuilder('b')
            ->select('b as banker, a')
            ->leftJoin('b.accounts', 'a')
            ->andWhere('a IS NULL')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        if ( count($bankers) > 0 ) {
            return $bankers[0]['banker'];
        }

        $bankers =  $this->createQueryBuilder('b')
            ->select('b as banker, COUNT(a.id) as nbAccount')
            ->leftJoin('b.accounts', 'a')
            ->groupBy('banker')
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;




        $index=0;
        for ( $i=0; $i< count($bankers); $i++ ) {
            $index = $bankers[$i]['nbAccount'] < $bankers[$index]['nbAccount'] ? $i : $index;
        }

        return $bankers[$index]['banker'];
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
