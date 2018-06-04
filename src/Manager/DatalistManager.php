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

    public function getDatalist(Datalist $datalist, LdapUser $user )
    {
        $result = $this->findBy(array (
            'device' => $datalist->getDevice(),
            'client' => $datalist->getClient()
        ));
        dump($result);
        die;
        $datalist->setName($datalist->getDevice()->getLibrary()."_".$datalist->getClient()->getShortName());
        $datalist->setUser($user);

        $this->save($datalist);

        return $datalist;
    }

}