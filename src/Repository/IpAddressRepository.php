<?php

namespace App\Repository;

use App\Entity\IpAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IpAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method IpAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method IpAddress[]    findAll()
 * @method IpAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IpAddressRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IpAddress::class);
    }

    // /**
    //  * @return IpAddress[] Returns an array of IpAddress objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IpAddress
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
