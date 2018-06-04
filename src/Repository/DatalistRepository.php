<?php

namespace App\Repository;

use App\Entity\Datalist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Datalist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Datalist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Datalist[]    findAll()
 * @method Datalist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatalistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Datalist::class);
    }

//    /**
//     * @return Datalist[] Returns an array of Datalist objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Datalist
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
