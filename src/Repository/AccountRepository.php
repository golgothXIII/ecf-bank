<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Banker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param Banker $banker
     * @return int number of account to validate by banker in param
     */
    public function numberOfAccountToValidate(Banker $banker) : int
    {
        $result = $this->createQueryBuilder('a')
            ->select('count(a.banker) as number')
            ->andWhere('a.bank_account_id IS NULL ')
            ->andWhere('a.banker = :val')
            ->setParameter('val', $banker->getId())
            ->groupBy('a.banker')
            ->getQuery()
            ->getResult()
        ;
        return empty($result) ? 0 : $result[0]['number'];
    }

    /**
     * @param Banker $banker
     * @return int number of account to delete by banker in param
     */
    public function numberOfAccountToDelete(Banker $banker) : int
    {
        $result = $this->createQueryBuilder('a')
            ->select('count(a.banker) as number')
            ->andWhere('a.toDeleted = true ')
            ->andWhere('a.banker = :val')
            ->setParameter('val', $banker->getId())
            ->groupBy('a.banker')
            ->getQuery()
            ->getResult()
        ;
        return empty($result) ? 0 : $result[0]['number'];
    }


    // /**
    //  * @return Account[] Returns an array of Account objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
