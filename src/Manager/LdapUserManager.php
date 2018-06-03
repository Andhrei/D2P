<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\LdapUser;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\Entry;

class LdapUserManager extends BaseManager
{

    public function __construct(LdapUserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = LdapUser::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function createFromEntry(Entry $entry)
    {
        $user = new LdapUser($entry);

        $this->save($user);

        return $user;
    }
}