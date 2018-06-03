<?php

namespace App\Repository;

use App\Entity\LdapUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LdapUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method LdapUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method LdapUser[]    findAll()
 * @method LdapUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LdapUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LdapUser::class);
    }

    public function findByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return LdapUser[] Returns an array of LdapUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LdapUser
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
