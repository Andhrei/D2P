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
use Symfony\Component\Process\Process;

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

    public function schedule(Schedule $schedule)
    {

        if($schedule->getRecurrence() == "DAILY")
        {
            $cmd = ("omnidbutil -create_schedule -specType BACKUP -appType datalist -specName ".$schedule->getDatalist()->getName()." -dpName ".$schedule->getName()." -dpType incr -recurrenceType ".$schedule->getRecurrence()." -everyNth 1 -startDate ".date("Y-m-d")." -startTime 12:00");
        } else {
            $cmd = ("omnidbutil -create_schedule -specType BACKUP -appType datalist -specName ".$schedule->getDatalist()->getName()." -dpName ".$schedule->getName()." -dpType incr -recurrenceType ".$schedule->getRecurrence()." -everyNth 1 -daysOfWeek 1 -startDate ".date("Y-m-d")." -startTime 12:00");
        }

        dump($cmd);
        $proc = new Process($cmd);
        $proc->run();

    }
}