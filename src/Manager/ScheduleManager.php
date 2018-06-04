<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\LdapUser;
use App\Entity\Schedule;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\Entry;

class ScheduleManager extends BaseManager
{

    public function __construct(LdapUserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = Schedule::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function addScheduleForUser(Schedule $schedule, LdapUser $user)
    {
    }
}