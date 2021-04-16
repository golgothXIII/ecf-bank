<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Transfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transfer[]    findAll()
 * @method Transfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfer::class);
    }

    /**
     * @param beneficiary $account
     * @return float Returns balance for a account
     */
    public function findBalanceAccount(Account $account) : float
    {
        $result = $this->createQueryBuilder('t')
            ->select('SUM(t.amount) as balance')
            ->andWhere('t.account = :val')
            ->setParameter('val', $account)
            ->groupBy('t.account')
            ->getQuery()
            ->getResult()
        ;
        return $result[0]['balance'];
    }

    /**
     * @param beneficiary $account
     * @return int Returns number of transfer a account
     */
    public function numberOfTransfers(Account $account) : int
    {
        $result = $this->createQueryBuilder('t')
            ->select('count(t.amount) as number')
            ->andWhere('t.account = :val')
            ->setParameter('val', $account)
            ->groupBy('t.account')
            ->getQuery()
            ->getResult()
        ;
        return $result[0]['number'];
    }

    /*
    public function findOneBySomeField($value): ?Transfer
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
