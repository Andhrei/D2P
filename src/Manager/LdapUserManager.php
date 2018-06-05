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
    private $devManager;

    public function __construct(LdapUserRepository $repository, EntityManagerInterface $entityManager, DeviceManager $devManager)
    {
        $this->class = LdapUser::class;
        $this->repo = $repository;
        $this->em = $entityManager;
        $this->devManager = $devManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function createFromEntry(Entry $entry)
    {
        $user = new LdapUser($entry);
        $this->save($user);
        $this->devManager->createAzureDeviceForUser($user);
        $this->devManager->createLocalDeviceForUser($user);

        return $user;
    }
}