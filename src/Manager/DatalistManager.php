<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\Client;
use App\Entity\Datalist;
use App\Entity\Device;
use App\Entity\LdapUser;
use App\Form\DatalistType;
use App\Repository\ClientRepository;
use App\Repository\DatalistRepository;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Process\Process;

class DatalistManager extends BaseManager
{

    public function __construct(DatalistRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = Datalist::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function getOrCreate(Client $client, Device $device, LdapUser $user)
    {
        $datalist = $this->findOneBy(array(
            'client' => $client,
            'device' => $device,
            'user' => $user
        ));


        if (!$datalist) {
            $datalist = new Datalist($client,$device,$user);

            $proc = new Process("omnicreatedl -datalist ".$datalist->getName()." -host ".$client->getHostname()." -device ".$device->getName());
            $proc->run();

            $this->save($datalist);
        }

        return $datalist;
    }

    public function getDatalist(Datalist $datalist, LdapUser $user )
    {
        $result = $this->findOneBy(array (
            'device' => $datalist->getDevice(),
            'client' => $datalist->getClient(),
            'user' => $user
        ));
        if ($result)
        {
            return $result;
        } else {
            $datalist->setUser($user);
            $datalist->setType("Azure");
            $datalist->generateName();

            $this->save($datalist);

            $proc = new Process("omnicreatedl -datalist ".$datalist->getName()." -host ".$datalist->getClient()->getHostname()." -device ".$datalist->getDevice()->getName());
            $proc->run();

            return $datalist;
        }
    }

    public function backup(Datalist $datalist) {
        dump('backikng up '.$datalist->getName());
        $proc = new Process("omnib -datalist ".$datalist->getName()." -mode incremental");
        $proc->start();

    }

}