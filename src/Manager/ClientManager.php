<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 4:22 PM
 */

namespace App\Manager;


use App\Entity\Client;
use App\Entity\LdapUser;
use App\Repository\ClientRepository;
use App\Repository\LdapUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Process\Process;

class ClientManager extends BaseManager
{

    public function __construct(ClientRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->class = Client::class;
        $this->repo = $repository;
        $this->em = $entityManager;
    }

    public function filterBy($filters)
    {
        // TODO: Implement filterBy() method.
    }

    public function updateClientList()
    {
//        $process = new Process('');
    }

}