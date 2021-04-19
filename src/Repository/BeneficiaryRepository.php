<?php

namespace App\Repository;

use App\Entity\Banker;
use App\Entity\Beneficiary;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Beneficiary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beneficiary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beneficiary[]    findAll()
 * @method Beneficiary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeneficiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beneficiary::class);
    }

    /**
     * @param Banker $banker
     * @return int number of account to validate by banker in param
     */
    public function numberOfBeneficiariesToValidate(Banker $banker) : int
    {
        $result = $this->createQueryBuilder('b')
            ->select('count(b.banker) as number')
            ->andWhere('b.isValidated = false ')
            ->andWhere('b.banker = :val')
            ->setParameter('val', $banker->getId())
            ->groupBy('b.banker')
            ->getQuery()
            ->getResult()
        ;
        return empty($result) ? 0 : $result[0]['number'];
    }

    /**
     * @param Customer $customer
     * @return int number of account to validate by banker in param
     */
    public function numberOfBeneficiaries(Customer $customer) : int
    {
        $result = $this->createQueryBuilder('b')
            ->select('count(b.customer) as number')
            ->andWhere('b.customer = :val')
            ->setParameter('val', $customer->getId())
            ->groupBy('b.customer')
            ->getQuery()
            ->getResult()
        ;
        return empty($result) ? 0 : $result[0]['number'];
    }

    // /**
    //  * @return Beneficiary[] Returns an array of Beneficiary objects
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
    public function findOneBySomeField($value): ?Beneficiary
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
